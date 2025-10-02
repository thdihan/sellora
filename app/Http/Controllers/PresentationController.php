<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Services\PresentationFileService;
use App\Services\PresentationTrackingService;
use App\Services\PresentationGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;

/**
 * PresentationController
 *
 * Handles CRUD operations for presentations
 */
class PresentationController extends Controller
{
    protected PresentationFileService $fileService;
    protected PresentationTrackingService $trackingService;

    public function __construct(PresentationFileService $fileService, PresentationTrackingService $trackingService)
    {
        $this->fileService = $fileService;
        $this->trackingService = $trackingService;
    }

    /**
     * Display a listing of presentations
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Presentation::with('user')
            ->where('user_id', Auth::id())
            ->orWhere('is_public', true);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Template filter
        if ($request->filled('is_template')) {
            $query->where('is_template', $request->boolean('is_template'));
        }

        $presentations = $query->latest()->paginate(12);

        return view('presentations.index', compact('presentations'));
    }

    /**
     * Show the form for creating a new presentation
     *
     * @return View
     */
    public function create(): View
    {
        return view('presentations.create');
    }

    /**
     * Store a newly created presentation
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:ppt,pptx,pdf,odp|max:51200', // 50MB max
            'category' => 'required|string|in:general,business,education,marketing,technology',
            'tags' => 'nullable|string',
            'status' => 'required|string|in:draft,published,archived',
            'is_public' => 'boolean',
            'is_template' => 'boolean'
        ]);

        try {
            // Upload file
            $fileData = $this->fileService->uploadFile(
                $request->file('file'),
                Auth::id()
            );

            // Create presentation
            $presentation = Presentation::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $fileData['file_path'],
                'original_filename' => $fileData['original_filename'],
                'file_size' => $fileData['file_size'],
                'mime_type' => $fileData['mime_type'],
                'category' => $request->category,
                'tags' => $request->tags,
                'status' => $request->status,
                'is_public' => $request->boolean('is_public'),
                'is_template' => $request->boolean('is_template'),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('presentations.show', $presentation)
                ->with('success', 'Presentation uploaded successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['file' => 'Failed to upload presentation: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified presentation
     *
     * @param Presentation $presentation
     * @return View
     */
    public function show(Presentation $presentation, Request $request): View
    {
        // Check if user can view this presentation
        if (!$presentation->is_public && $presentation->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to view this presentation.');
        }

        // Track the view using the tracking service
        $this->trackingService->trackView($presentation, $request);

        $presentation->load(['user', 'comments.user', 'views', 'downloads']);
        
        return view('presentations.show', compact('presentation'));
    }

    /**
     * Show the form for editing the specified presentation
     *
     * @param Presentation $presentation
     * @return View
     */
    public function edit(Presentation $presentation): View
    {
        // Check if user can edit this presentation
        if (!$presentation->canBeEditedBy(Auth::id())) {
            abort(403, 'You do not have permission to edit this presentation.');
        }

        return view('presentations.edit', compact('presentation'));
    }

    /**
     * Update the specified presentation
     *
     * @param Request $request
     * @param Presentation $presentation
     * @return RedirectResponse
     */
    public function update(Request $request, Presentation $presentation): RedirectResponse
    {
        // Check if user can edit this presentation
        if (!$presentation->canBeEditedBy(Auth::id())) {
            abort(403, 'You do not have permission to edit this presentation.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:ppt,pptx,pdf,odp|max:51200', // 50MB max
            'category' => 'required|string|in:general,business,education,marketing,technology',
            'tags' => 'nullable|string',
            'status' => 'required|string|in:draft,published,archived',
            'is_public' => 'boolean',
            'is_template' => 'boolean'
        ]);

        try {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'tags' => $request->tags,
                'status' => $request->status,
                'is_public' => $request->boolean('is_public'),
                'is_template' => $request->boolean('is_template')
            ];

            // Handle file replacement
            if ($request->hasFile('file')) {
                // Delete old file
                $this->fileService->deleteFile($presentation->file_path);
                
                // Upload new file
                $fileData = $this->fileService->uploadFile(
                    $request->file('file'),
                    Auth::id()
                );
                
                $updateData = array_merge($updateData, $fileData);
            }

            $presentation->update($updateData);

            return redirect()->route('presentations.show', $presentation)
                ->with('success', 'Presentation updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['file' => 'Failed to update presentation: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified presentation
     *
     * @param Presentation $presentation
     * @return RedirectResponse
     */
    public function destroy(Presentation $presentation): RedirectResponse
    {
        // Check if user can delete this presentation
        if (!$presentation->canBeEditedBy(Auth::id())) {
            abort(403, 'You do not have permission to delete this presentation.');
        }

        try {
            // Delete file
            $this->fileService->deleteFile($presentation->file_path);
            
            // Delete presentation (soft delete)
            $presentation->delete();

            return redirect()->route('presentations.index', request()->query())
                ->with('success', 'Presentation deleted successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete presentation: ' . $e->getMessage()]);
        }
    }

    /**
     * Download presentation file
     *
     * @param Presentation $presentation
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Presentation $presentation, Request $request)
    {
        // Check if user can download this presentation
        if (!$presentation->is_public && $presentation->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to download this presentation.');
        }

        // Track the download using the tracking service
        $this->trackingService->trackDownload($presentation, $request);

        $filePath = storage_path('app/public/' . $presentation->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $presentation->original_filename);
    }

    /**
     * Display auto reports for presentations.
     */
    public function autoReports(Request $request): View
    {
        $user = auth()->user();
        
        // Get presentations with analytics data
        $presentations = Presentation::with(['user', 'analytics'])
            ->where('user_id', $user->id)
            ->orWhere('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate summary statistics
        $totalPresentations = $presentations->total();
        $totalViews = $presentations->sum('view_count');
        $totalDownloads = $presentations->sum('download_count');
        $avgRating = $presentations->avg('rating');

        return view('presentations.auto-reports', compact(
            'presentations',
            'totalPresentations',
            'totalViews', 
            'totalDownloads',
            'avgRating'
        ));
    }

    /**
     * Generate a presentation from a report.
     */
    public function generateFromReport(Request $request, $reportId)
    {
        $user = auth()->user();
        
        // Check if user has NSM+ permissions to generate presentations
        $nsmPlusRoles = ['NSM', 'NSM+', 'RSM', 'ASM', 'Author'];
        if (!in_array($user->role, $nsmPlusRoles)) {
            abort(403, 'You do not have permission to generate presentations.');
        }

        try {
            $presentationGenerator = app(PresentationGeneratorService::class);
            $presentation = $presentationGenerator->generateFromReport($reportId, $user->id);
            
            return redirect()->route('presentations.show', $presentation)
                ->with('success', 'Presentation generated successfully from report.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate presentation: ' . $e->getMessage());
        }
    }

    /**
     * Export presentations data
     *
     * @param Request $request
     * @return Response
     */
    public function export(Request $request): Response
    {
        $format = $request->get('format', 'csv');
        $presentations = Presentation::with('user')
            ->when(Auth::user()->role && Auth::user()->role->name !== 'Admin', fn($q) => $q->where('user_id', Auth::id()))
            ->get();

        switch ($format) {
            case 'csv':
                return $this->_exportCsv($presentations);
            case 'pdf':
                return $this->_exportPdf($presentations);
            case 'excel':
                return $this->_exportExcel($presentations);
            case 'word':
                return $this->_exportWord($presentations);
            default:
                return back()->with('error', 'Invalid export format.');
        }
    }

    /**
     * Export presentations as Excel
     *
     * @param \Illuminate\Database\Eloquent\Collection $presentations
     * @return Response
     */
    private function _exportExcel($presentations): Response
    {
        $exportData = [];
        
        // Add headers
        $exportData[] = ['ID', 'Title', 'Category', 'Status', 'Views', 'Downloads', 'Created By', 'Created At'];
        
        // Add data rows
        foreach ($presentations as $presentation) {
            $exportData[] = [
                $presentation->id,
                $presentation->title,
                $presentation->category,
                $presentation->status,
                $presentation->view_count,
                $presentation->download_count,
                $presentation->user->name,
                $presentation->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        return Excel::download(
            new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function array(): array {
                    return $this->data;
                }
            },
            'presentations_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export presentations as Word document
     *
     * @param \Illuminate\Database\Eloquent\Collection $presentations
     * @return Response
     */
    private function _exportWord($presentations): Response
    {
        $html = view('presentations.export-word', compact('presentations'))->render();
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="presentations_' . date('Y-m-d') . '.docx"'
        ];
        
        // For now, export as HTML that can be opened in Word
        // In production, you might want to use PhpOffice/PhpWord for proper .docx format
        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="presentations_' . date('Y-m-d') . '.doc"'
        ]);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExportJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:Admin,Author,Manager');
    }
    public function index(Request $request): JsonResponse
    {
        $jobs = ExportJob::with('creator')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->scope, fn($q) => $q->where('scope', $request->scope))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($jobs);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scope' => 'required|in:full,partial',
            'modules' => 'required|array|min:1',
            'modules.*' => 'string|max:50',
            'format' => 'required|in:sql,csv,excel',
            'filters' => 'nullable|array',
            'include_dependencies' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job = ExportJob::create([
            'scope' => $request->scope,
            'modules' => $request->modules,
            'format' => $request->format,
            'filters' => $request->filters ?? [],
            'include_dependencies' => $request->boolean('include_dependencies', false),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return response()->json($job->load('creator'), 201);
    }

    public function show(ExportJob $job): JsonResponse
    {
        return response()->json($job->load('creator'));
    }

    public function download(ExportJob $job): BinaryFileResponse|JsonResponse
    {
        if (!$job->isCompleted()) {
            return response()->json(['error' => 'Export not completed yet'], 400);
        }

        if (!$job->file_path || !Storage::disk('local')->exists($job->file_path)) {
            return response()->json(['error' => 'Export file not found'], 404);
        }

        $filename = sprintf(
            'export_%s_%s.%s',
            implode('_', $job->modules),
            $job->created_at->format('Y-m-d_H-i-s'),
            $job->format === 'excel' ? 'xlsx' : $job->format
        );

        return response()->download(
            Storage::disk('local')->path($job->file_path),
            $filename
        );
    }

    public function destroy(ExportJob $job): JsonResponse
    {
        if ($job->isProcessing()) {
            return response()->json(['error' => 'Cannot delete job while processing'], 400);
        }

        if ($job->file_path && Storage::disk('local')->exists($job->file_path)) {
            Storage::disk('local')->delete($job->file_path);
        }

        $job->delete();

        return response()->json(['message' => 'Export job deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImportJob;
use App\Models\ImportPreset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:Admin,Author,Manager');
    }
    public function index(Request $request): JsonResponse
    {
        $jobs = ImportJob::with('creator')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->module, fn($q) => $q->where('module', $request->module))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($jobs);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source_type' => 'required|in:csv,excel,sql',
            'module' => 'required|string|max:50',
            'file' => 'required|file|max:10240',
            'preset_id' => 'nullable|exists:import_presets,id',
            'options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filePath = $request->file('file')->store('imports', 'local');
        
        $job = ImportJob::create([
            'source_type' => $request->source_type,
            'module' => $request->module,
            'file_path' => $filePath,
            'preset_id' => $request->preset_id,
            'options' => $request->options ?? [],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return response()->json($job->load('creator'), 201);
    }

    public function show(ImportJob $job): JsonResponse
    {
        return response()->json($job->load(['creator', 'items']));
    }

    public function destroy(ImportJob $job): JsonResponse
    {
        if ($job->isProcessing()) {
            return response()->json(['error' => 'Cannot delete job while processing'], 400);
        }

        if ($job->file_path && Storage::disk('local')->exists($job->file_path)) {
            Storage::disk('local')->delete($job->file_path);
        }

        $job->delete();

        return response()->json(['message' => 'Import job deleted successfully']);
    }

    public function presets(): JsonResponse
    {
        $presets = ImportPreset::with('creator')
            ->orderBy('name')
            ->get();

        return response()->json($presets);
    }

    public function storePreset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'source_type' => 'required|in:csv,excel,sql',
            'module' => 'required|string|max:50',
            'column_map' => 'required|array',
            'options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $preset = ImportPreset::create([
            'name' => $request->name,
            'source_type' => $request->source_type,
            'module' => $request->module,
            'column_map' => $request->column_map,
            'options' => $request->options ?? [],
            'created_by' => auth()->id(),
        ]);

        return response()->json($preset->load('creator'), 201);
    }
}

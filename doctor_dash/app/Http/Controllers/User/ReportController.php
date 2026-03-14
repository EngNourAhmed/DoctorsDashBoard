<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use App\Notifications\CaseActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = $user->reports()
            ->selectRaw('MIN(id) as id, MAX(batch_id) as batch_id, MIN(title) as title, MIN(description) as description, MIN(created_at) as created_at, MIN(file_path) as file_path, MIN(original_name) as original_name, MIN(mime_type) as mime_type, MIN(status) as status, MIN(updated_by) as updated_by, COUNT(*) as files_count')
            ->groupByRaw('CASE WHEN batch_id IS NULL THEN id ELSE batch_id END')
            ->with('updatedBy')
            ->latest('created_at');

        if ($request->query('filter') === 'pending') {
            $query->having('status', 'Pending');
        } elseif ($request->query('filter') === 'reviewed') {
            $query->having('status', '!=', 'Pending');
        }

        $reports = $query->paginate(6)->withQueryString();

        return view('user.reports.index', [
            'user' => $user,
            'reports' => $reports,
            'statuses' => Report::STATUSES,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return view('user.reports.create', [
            'user' => $user,
        ]);
    }

    public function uploadTemp(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file'], // Removed max size limit
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $filename = \Illuminate\Support\Str::random(40) . ($extension ? '.' . $extension : '');
        $path = $file->storeAs('temp', $filename, 'public');

        return response()->json([
            'ok' => true,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file'], // Removed max size limit
            'temp_paths' => ['nullable', 'array'],
            'temp_paths.*' => ['string'],
        ]);

        $batchId = (string) \Illuminate\Support\Str::uuid();
        $processedTempFiles = false;

        // Handle temporary uploads
        if (!empty($data['temp_paths'])) {
            foreach ($data['temp_paths'] as $tempPath) {
                if (Storage::disk('public')->exists($tempPath)) {
                    $filename = basename($tempPath);
                    $newPath = 'reports/' . $filename;
                    Storage::disk('public')->move($tempPath, $newPath);

                    Report::create([
                        'user_id' => $user->id,
                        'batch_id' => $batchId,
                        'title' => $data['title'],
                        'description' => $data['description'] ?? null,
                        'file_path' => $newPath,
                        'original_name' => $request->input('original_names.' . str_replace('.', '_', $tempPath), $filename),
                        'mime_type' => $request->input('mime_types.' . str_replace('.', '_', $tempPath), 'application/octet-stream'),
                        'size' => $request->input('sizes.' . str_replace('.', '_', $tempPath), 0),
                        'status' => 'Pending',
                    ]);
                    $processedTempFiles = true;
                }
            }
        }

        // Handle direct uploads (fallback)
        // Only process these if no temp_paths were processed to avoid duplication
        if (!$processedTempFiles && !empty($data['files'])) {
            foreach ($data['files'] as $file) {
                $extension = $file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $filename = \Illuminate\Support\Str::random(40) . ($extension ? '.' . $extension : '');
                $path = $file->storeAs('reports', $filename, 'public');

                Report::create([
                    'user_id' => $user->id,
                    'batch_id' => $batchId,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'status' => 'Pending',
                ]);
            }
        }

        // Notify all staff about the new case
        $staff = User::whereIn('role', ['admin', 'assistant', 'admin_assistant'])->get();
        foreach ($staff as $person) {
            $person->notify(new CaseActivity(Report::where('batch_id', $batchId)->first(), 'created'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'redirect' => route('user.reports.index'),
                'message' => 'Case uploaded successfully.',
            ]);
        }

        return redirect()->route('user.reports.index')->with('status', 'Case uploaded successfully.');
    }

    public function show(Request $request, $batchId)
    {
        $user = $request->user();
        
        // Verify user owns at least one report in this batch
        $ownsBatch = Report::where('user_id', $user->id)
            ->where('batch_id', $batchId)
            ->exists();

        if (!$ownsBatch) {
            // Check if it's an ID for backward compatibility
            $report = Report::where('user_id', $user->id)->find($batchId);
            if ($report) {
                $batchId = $report->batch_id;
            } else {
                abort(404);
            }
        }

        // Now fetch ALL reports in this batch (including replies from admins)
        $allReports = Report::where('batch_id', $batchId)
            ->orderByDesc('created_at')
            ->get();

        $caseFiles = $allReports->where('is_reply', false);
        $adminReplies = $allReports->where('is_reply', true);

        // Fetch text replies from Case Chat Conversation
        $adminMessages = collect();
        $conversation = \App\Models\Conversation::where('type', 'case_chat')
            ->where('batch_id', $batchId)
            ->first();
        
        if ($conversation) {
            $adminMessages = $conversation->messages()
                ->whereHas('sender', function($q) {
                    $q->whereIn('role', ['admin', 'assistant', 'admin_assistant']);
                })
                ->with('sender')
                ->latest()
                ->get();
        }

        return view('user.reports.show', [
            'user' => $user,
            'reports' => $caseFiles,
            'adminReplies' => $adminReplies,
            'adminMessages' => $adminMessages,
            'allReports' => $allReports,
            'title' => $allReports->first()->title ?? 'Case Details',
            'batch_id' => $batchId
        ]);
    }

    public function edit(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($report->user_id === $user->id, 404);

        return view('user.reports.edit', [
            'user' => $user,
            'report' => $report,
        ]);
    }

    public function update(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($report->user_id === $user->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file'], // Removed max size limit
        ]);

        if ($request->hasFile('file')) {
            if ($report->file_path) {
                Storage::disk('public')->delete($report->file_path);
            }

            $file = $data['file'];
            $extension = $file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $filename = \Illuminate\Support\Str::random(40) . ($extension ? '.' . $extension : '');
            $path = $file->storeAs('reports', $filename, 'public');

            $report->file_path = $path;
            $report->original_name = $file->getClientOriginalName();
            $report->mime_type = $file->getClientMimeType();
            $report->size = $file->getSize();
        }

        if ($report->batch_id) {
            Report::where('batch_id', $report->batch_id)->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);
        } else {
            $report->title = $data['title'];
            $report->description = $data['description'] ?? null;
            $report->save();
        }

        // Notify all staff about the case update
        $staff = User::whereIn('role', ['admin', 'assistant', 'admin_assistant'])->get();
        foreach ($staff as $person) {
            $person->notify(new CaseActivity($report, 'updated'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'redirect' => route('user.reports.index'),
                'message' => 'Case updated successfully.',
            ]);
        }

        return redirect()->route('user.reports.index')->with('status', 'Case updated successfully.');
    }

    public function destroy(Request $request, Report $report)
    {
        $user = $request->user();

        abort_unless($report->user_id === $user->id, 404);

        if ($report->batch_id) {
            $batchReports = Report::where('batch_id', $report->batch_id)->get();
            foreach ($batchReports as $br) {
                if ($br->file_path) {
                    Storage::disk('public')->delete($br->file_path);
                }
                $br->delete();
            }
        } else {
            if ($report->file_path) {
                Storage::disk('public')->delete($report->file_path);
            }
            $report->delete();
        }

        // Notify all staff about case deletion
        $staff = User::whereIn('role', ['admin', 'assistant', 'admin_assistant'])->get();
        foreach ($staff as $person) {
            // Use local copy of report since it's deleted from DB
            $person->notify(new CaseActivity($report, 'deleted'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'redirect' => route('user.reports.index'),
                'message' => 'Case deleted successfully.',
            ]);
        }

        return redirect()->route('user.reports.index')->with('status', 'Case deleted successfully.');
    }

    public function download(Request $request, Report $report)
    {
        $user = $request->user();

        // Verify user owns the batch this report belongs to
        $ownsBatch = Report::where('user_id', $user->id)
            ->where('batch_id', $report->batch_id)
            ->exists();

        abort_unless($ownsBatch || in_array($user->role, ['admin', 'assistant', 'admin_assistant']), 404);

        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($report->file_path, $report->original_name, [
            'Content-Type' => $report->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function preview(Request $request, Report $report)
    {
        $user = $request->user();

        // Verify user owns the batch this report belongs to
        $ownsBatch = Report::where('user_id', $user->id)
            ->where('batch_id', $report->batch_id)
            ->exists();

        abort_unless($ownsBatch || in_array($user->role, ['admin', 'assistant', 'admin_assistant']), 404);

        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($report->file_path), [
            'Content-Type' => $report->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $report->original_name . '"',
        ]);
    }

    public function uploadAdditional(Request $request, $batchId)
    {
        try {
            $user = $request->user();
            
            // Verify ownership
            $existingReport = Report::where('batch_id', $batchId)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$existingReport) {
                return response()->json(['error' => 'Case not found or access denied'], 404);
            }
            
            $request->validate([
                'files' => 'required|array',
                'files.*' => 'file', // Removed max size limit
            ]);
            
            $uploadedFiles = [];
            
            foreach ($request->file('files') as $file) {
                $extension = $file->getClientOriginalExtension() ?: 
                    pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $filename = \Illuminate\Support\Str::random(40) . 
                    ($extension ? '.' . $extension : '');
                $path = $file->storeAs('reports', $filename, 'public');
                
                if (!$path) {
                    throw new \Exception('Failed to store file: ' . $file->getClientOriginalName());
                }
                
                $report = Report::create([
                    'user_id' => $user->id,
                    'batch_id' => $batchId,
                    'title' => $existingReport->title,
                    'description' => $existingReport->description,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'status' => $existingReport->status,
                ]);
                
                $uploadedFiles[] = $report;
            }
            
            // Notify staff
            $staff = User::whereIn('role', ['admin', 'assistant', 'admin_assistant'])->get();
            foreach ($staff as $person) {
                $person->notify(new CaseActivity($existingReport, 'file_added'));
            }
            
            return response()->json([
                'ok' => true,
                'files' => $uploadedFiles,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload files. Please try again.'], 500);
        }
    }

    public function generateFileLink(Request $request, Report $report)
    {
        $user = $request->user();
        
        // Verify access
        if ($report->user_id !== $user->id && 
            !in_array($user->role, ['admin', 'assistant', 'admin_assistant'])) {
            abort(403);
        }
        
        $url = route('reports.file.preview', [
            'batchId' => (string)$report->batch_id,
            'fileId' => (string)$report->id,
            'signature' => $this->generateSignature($report),
        ]);
        
        return response()->json(['url' => $url]);
    }

    public function generateBatchLink(Request $request, $batchId)
    {
        $user = $request->user();
        
        $report = Report::where('batch_id', $batchId)->first();
        if (!$report) {
            return response()->json(['error' => 'Case not found'], 404);
        }
        
        // Verify access
        if ($report->user_id !== $user->id && 
            !in_array($user->role, ['admin', 'assistant', 'admin_assistant'])) {
            abort(403);
        }
        
        $url = route('reports.batch.shared', [
            'batchId' => (string)$batchId,
            'signature' => hash_hmac('sha256', 'batch_' . (string)$batchId, (string)config('app.key')),
        ]);
        
        return response()->json(['url' => $url]);
    }

    public function sharedFile(Request $request, $batchId, $fileId)
    {
        // Verify signature
        if (!$this->verifySignature($request, $fileId)) {
            abort(403, 'Invalid or expired link');
        }
        
        $report = Report::where('id', $fileId)
            ->where('batch_id', $batchId)
            ->first();
        
        if (!$report) {
            abort(404);
        }
        
        // If has valid signature, we allow download without further user checks
        
        if (!Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }
        
        return Storage::disk('public')->download(
            $report->file_path, 
            $report->original_name,
            ['Content-Type' => $report->mime_type ?: 'application/octet-stream']
        );
    }

    public function sharedPreview(Request $request, $batchId, $fileId)
    {
        // Verify signature
        if (!$this->verifySignature($request, $fileId)) {
            abort(403, 'Invalid or expired link');
        }
        
        $report = Report::where('id', $fileId)
            ->where('batch_id', $batchId)
            ->first();
        
        if (!$report || !Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }
        
        return response()->file(Storage::disk('public')->path($report->file_path), [
            'Content-Type' => $report->mime_type ?: 'application/octet-stream',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function sharedBatch(Request $request, $batchId)
    {
        // Verify signature
        $expected = hash_hmac('sha256', 'batch_' . (string)$batchId, (string)config('app.key'));
        if (!hash_equals($expected, (string)$request->query('signature'))) {
            abort(403, 'Invalid or expired link');
        }
        
        $reports = Report::where('batch_id', $batchId)->get();
        
        if ($reports->isEmpty()) {
            abort(404);
        }
        
        return view('shared.batch', [
            'reports' => $reports,
            'title' => $reports->first()->title,
            'batch_id' => $batchId,
            'signature' => $request->query('signature')
        ]);
    }

    public function downloadBatch(Request $request, $batchId)
    {
        $user = $request->user();
        
        $reports = Report::where('user_id', $user->id)
            ->where('batch_id', $batchId)
            ->get();

        if ($reports->isEmpty()) {
            abort(404);
        }

        // IMPROVED: If only one file, serve it directly instead of zipping
        if ($reports->count() === 1) {
            $report = $reports->first();
            if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                return Storage::disk('public')->download($report->file_path, $report->original_name, [
                    'Content-Type' => $report->mime_type ?: 'application/octet-stream',
                ]);
            }
            abort(404);
        }

        $zip = new \ZipArchive();
        $fileName = 'case_collection_' . $batchId . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            foreach ($reports as $report) {
                if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                    $fullPath = Storage::disk('public')->path($report->file_path);
                    $zip->addFile($fullPath, $report->original_name);
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'Could not create zip file');
        }

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    protected function generateSignature($report)
    {
        return hash_hmac('sha256', 'file_' . (string)$report->id . '_' . (string)$report->batch_id, (string)config('app.key'));
    }

    protected function verifySignature($request, $fileId)
    {
        $signature = (string)$request->query('signature');
        $batchId = (string)$request->route('batchId');
        $expected = hash_hmac('sha256', 'file_' . (string)$fileId . '_' . (string)$batchId, (string)config('app.key'));
        return hash_equals($expected, $signature);
    }
}

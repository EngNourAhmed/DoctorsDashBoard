<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
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

        $reports = $query->paginate(10)->withQueryString();

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
            'file' => ['required', 'file', 'max:512000'],
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
            'files.*' => ['file', 'max:512000'],
            'temp_paths' => ['nullable', 'array'],
            'temp_paths.*' => ['string'],
        ]);

        $batchId = (string) \Illuminate\Support\Str::uuid();

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
                    ]);
                }
            }
        }

        // Handle direct uploads (fallback)
        if (!empty($data['files'])) {
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
                ]);
            }
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
        
        $reports = Report::where('user_id', $user->id)
            ->where('batch_id', $batchId)
            ->get();

        if ($reports->isEmpty()) {
            // Check if it's an ID for backward compatibility with single files
            $report = Report::where('user_id', $user->id)->find($batchId);
            if ($report) {
                $reports = collect([$report]);
            } else {
                abort(404);
            }
        }

        return view('user.reports.show', [
            'user' => $user,
            'reports' => $reports,
            'title' => $reports->first()->title ?? 'Case Details',
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
            // السماح بأي نوع ملف وأي حجم (الحد الفعلي يعتمد على إعدادات الخادم PHP)
            'file' => ['nullable', 'file', 'max:512000'],
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

        $report->title = $data['title'];
        $report->description = $data['description'] ?? null;
        $report->save();

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

        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();

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

        abort_unless($report->user_id === $user->id, 404);

        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($report->file_path, $report->original_name, [
            'Content-Type' => $report->mime_type ?: 'application/octet-stream',
        ]);
    }
}

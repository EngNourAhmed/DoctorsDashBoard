<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Notifications\ReportStatusUpdated;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Report::with(['user', 'updatedBy'])
            ->selectRaw('MIN(id) as id, MAX(batch_id) as batch_id, MIN(user_id) as user_id, MIN(title) as title, MIN(description) as description, MIN(created_at) as created_at, MIN(file_path) as file_path, MIN(original_name) as original_name, MIN(mime_type) as mime_type, MIN(status) as status, COUNT(*) as files_count, MAX(updated_by) as updated_by')
            ->groupByRaw('CASE WHEN batch_id IS NULL THEN id ELSE batch_id END')
            ->latest('created_at');

        if ($status) {
            if ($status === 'Other') {
                $query->having('status', '!=', 'Pending');
            } else {
                $query->having('status', $status);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reports = $query->paginate(20)->withQueryString();

        return view('admin.cases.index', [
            'reports' => $reports,
            'statusFilter' => $status,
            'searchFilter' => $search,
            'statuses' => Report::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(Report::STATUSES)),
        ]);

        try {
            $oldStatus = $report->status;
            $newStatus = $request->status;
            $reviewedAt = null;

            if (in_array($newStatus, ['Completed', 'Case Shipped', 'Case Shipped/Guide STL Shared'])) {
                $reviewedAt = $report->reviewed_at ?? now();
            }

            if ($report->batch_id) {
                Report::where('batch_id', $report->batch_id)->update([
                    'status' => $newStatus,
                    'reviewed_at' => $reviewedAt,
                    'updated_by' => auth()->id()
                ]);
                $report->status = $newStatus; // Update local instance for response
                $report->updated_by = auth()->id();
            } else {
                $report->status = $newStatus;
                $report->reviewed_at = $reviewedAt;
                $report->updated_by = auth()->id();
                $report->save();
            }

            // Notify the user about the status change
            if ($report->user) {
                $report->user->notify(new ReportStatusUpdated($report, $newStatus, $oldStatus));
            }

            // Also notify other admins/assistants about this update
            $staff = \App\Models\User::whereIn('role', ['admin', 'assistant', 'admin_assistant'])->get();
            
            foreach ($staff as $person) {
                $person->notify(new ReportStatusUpdated($report, $newStatus, $oldStatus));
            }

            return response()->json([
                'success' => true,
                'status' => $report->status,
                'class' => Report::STATUSES[$report->status],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Status Update Failed: ' . $e->getMessage(), [
                'report_id' => $report->id,
                'batch_id' => $report->batch_id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(Request $request, Report $report)
    {
        if (!$report->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($report->file_path, $report->original_name, [
            'Content-Type' => $report->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function preview(Request $request, Report $report)
    {
        if (!$report->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($report->file_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $report->file_path), [
            'Content-Type' => $report->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $report->original_name . '"',
        ]);
    }

    public function batch($batch_id)
    {
        $reports = Report::where('batch_id', $batch_id)->with('user')->get();

        if ($reports->isEmpty()) {
            abort(404);
        }

        return view('admin.cases.batch', [
            'reports' => $reports,
            'title' => $reports->first()->title,
            'batch_id' => $batch_id
        ]);
    }
}

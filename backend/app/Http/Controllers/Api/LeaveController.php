<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:menunggu,disetujui,ditolak',
            'leave_type' => 'nullable|in:izin,sakit,cuti,dinas_luar',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Leave::with(['user:id,name,email', 'approver:id,name']);

        // Role-based filtering
        if ($user->canApproveLeaves()) {
            // Admin/Waka can see all leaves or filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }
        } else {
            // Regular users can only see their own leaves
            $query->byUser($user->id);
        }

        // Apply filters
        if ($request->leave_type) {
            $query->byType($request->leave_type);
        }

        if ($request->start_date) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $leaves
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'leave_type' => 'required|in:izin,sakit,cuti,dinas_luar',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for overlapping leaves
        $overlappingLeave = Leave::byUser($user->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->first();

        if ($overlappingLeave) {
            return response()->json([
                'success' => false,
                'message' => 'You have an overlapping leave request for the selected dates'
            ], 422);
        }

        // Store attachment if provided
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'leave_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('leaves/attachments', $filename, 'public');
        }

        // Create leave request
        $leave = Leave::create([
            'user_id' => $user->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
            'status' => 'menunggu',
        ]);

        // Log activity
        AuditLog::log('leave_requested', [
            'leave_id' => $leave->id,
            'leave_type' => $request->leave_type,
            'duration_days' => $leave->getDurationDays(),
            'dates' => [$request->start_date, $request->end_date]
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Leave request submitted successfully',
            'data' => $leave->load(['user:id,name', 'approver:id,name'])
        ]);
    }

    public function show(Leave $leave)
    {
        $user = auth()->user();

        // Check permissions
        if (!$user->canApproveLeaves() && $leave->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this leave request'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $leave->load(['user:id,name,email', 'approver:id,name'])
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        $user = $request->user();

        if ($leave->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Leave request has already been processed'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update leave status
        $leave->update([
            'status' => 'disetujui',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // The database trigger will automatically create attendance records

        // Log activity
        AuditLog::log('leave_approved', [
            'leave_id' => $leave->id,
            'user_id' => $leave->user_id,
            'leave_type' => $leave->leave_type,
            'duration_days' => $leave->getDurationDays(),
            'comment' => $request->comment
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Leave request approved successfully',
            'data' => $leave->fresh(['user:id,name', 'approver:id,name'])
        ]);
    }

    public function reject(Request $request, Leave $leave)
    {
        $user = $request->user();

        if ($leave->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Leave request has already been processed'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update leave status
        $leave->update([
            'status' => 'ditolak',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        // Log activity
        AuditLog::log('leave_rejected', [
            'leave_id' => $leave->id,
            'user_id' => $leave->user_id,
            'leave_type' => $leave->leave_type,
            'duration_days' => $leave->getDurationDays(),
            'comment' => $request->comment
        ], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected',
            'data' => $leave->fresh(['user:id,name', 'approver:id,name'])
        ]);
    }
}
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user:id,name,email', 'user.role:id,role_name', 'approver:id,name'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaves = $query->paginate(20);

        // Get filter options
        $users = \App\Models\User::whereIn('role_id', [2, 3]) // Guru and Pegawai
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = ['menunggu', 'disetujui', 'ditolak'];
        $leaveTypes = ['izin', 'sakit', 'cuti', 'dinas_luar'];

        // Statistics
        $stats = [
            'pending' => Leave::where('status', 'menunggu')->count(),
            'approved_today' => Leave::where('status', 'disetujui')
                ->whereDate('approved_at', today())
                ->count(),
            'this_month' => Leave::whereYear('start_date', now()->year)
                ->whereMonth('start_date', now()->month)
                ->count(),
        ];

        return view('admin.leaves.index', compact(
            'leaves',
            'users',
            'statuses',
            'leaveTypes',
            'stats'
        ));
    }

    public function show(Leave $leave)
    {
        $leave->load(['user:id,name,email', 'user.role:id,role_name', 'approver:id,name']);
        
        return view('admin.leaves.show', compact('leave'));
    }

    public function approve(Request $request, Leave $leave)
    {
        if ($leave->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Izin sudah diproses sebelumnya.');
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update leave status
            $leave->update([
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
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
            ], auth()->id());

            return redirect()->route('admin.leaves.index')
                ->with('success', 'Izin berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyetujui izin: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Leave $leave)
    {
        if ($leave->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Izin sudah diproses sebelumnya.');
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update leave status
            $leave->update([
                'status' => 'ditolak',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Log activity
            AuditLog::log('leave_rejected', [
                'leave_id' => $leave->id,
                'user_id' => $leave->user_id,
                'leave_type' => $leave->leave_type,
                'duration_days' => $leave->getDurationDays(),
                'comment' => $request->comment
            ], auth()->id());

            return redirect()->route('admin.leaves.index')
                ->with('success', 'Izin berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menolak izin: ' . $e->getMessage());
        }
    }
}
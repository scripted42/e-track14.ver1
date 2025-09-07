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
        $user = auth()->user();
        
        // For non-admin users, only show their own leaves
        if (!$user->hasRole('Admin') && !$user->hasRole('Kepala Sekolah') && !$user->hasRole('Waka Kurikulum')) {
            $query = Leave::with(['user:id,name,email', 'user.role:id,role_name', 'approver:id,name'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc');
        } else {
            $query = Leave::with(['user:id,name,email', 'user.role:id,role_name', 'approver:id,name'])
                ->orderBy('created_at', 'desc');
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('user_id') && ($user->hasRole('Admin') || $user->hasRole('Kepala Sekolah') || $user->hasRole('Waka Kurikulum'))) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaves = $query->paginate(20);

        // Get filter options (only for admin/approvers)
        $users = collect();
        if ($user->hasRole('Admin') || $user->hasRole('Kepala Sekolah') || $user->hasRole('Waka Kurikulum')) {
            $users = \App\Models\User::whereIn('role_id', [2, 3, 4, 5]) // Guru, Pegawai, Kepala Sekolah, Waka Kurikulum
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $statuses = ['menunggu', 'disetujui', 'ditolak'];
        $leaveTypes = ['izin', 'sakit', 'cuti', 'dinas_luar'];

        // Statistics (respect role: non-admin sees only own data)
        $pendingQuery = Leave::where('status', 'menunggu');
        $approvedTodayQuery = Leave::where('status', 'disetujui')->whereDate('approved_at', today());
        $thisMonthQuery = Leave::whereYear('start_date', now()->year)->whereMonth('start_date', now()->month);

        if (!$user->hasRole('Admin') && !$user->hasRole('Kepala Sekolah') && !$user->hasRole('Waka Kurikulum')) {
            $pendingQuery->where('user_id', $user->id);
            $approvedTodayQuery->where('user_id', $user->id);
            $thisMonthQuery->where('user_id', $user->id);
        }

        $stats = [
            'pending' => $pendingQuery->count(),
            'approved_today' => $approvedTodayQuery->count(),
            'this_month' => $thisMonthQuery->count(),
            // Optional: rejected today only for display symmetry
            'rejected_today' => Leave::where('status', 'ditolak')
                ->when((!$user->hasRole('Admin') && !$user->hasRole('Kepala Sekolah') && !$user->hasRole('Waka Kurikulum')), function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereDate('approved_at', today())
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_type' => 'required|in:izin,sakit,cuti,dinas_luar',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:500',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Calculate duration
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $duration = $startDate->diffInDays($endDate) + 1;

            // Handle evidence upload
            $evidencePath = null;
            $evidenceOriginalName = null;
            if ($request->hasFile('evidence')) {
                $file = $request->file('evidence');
                $evidenceOriginalName = $file->getClientOriginalName();
                $evidencePath = $file->store('evidence', 'public');
            }

            // Create leave request
            $leave = Leave::create([
                'user_id' => auth()->id(),
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_days' => $duration,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'evidence_path' => $evidencePath,
                'evidence_original_name' => $evidenceOriginalName,
                'status' => 'menunggu',
            ]);

            // Log activity
            AuditLog::log('leave_created', [
                'leave_id' => $leave->id,
                'leave_type' => $leave->leave_type,
                'duration_days' => $duration,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ], auth()->id());

            return redirect()->route('admin.leaves.index')
                ->with('success', 'Pengajuan izin berhasil dikirim dan sedang menunggu persetujuan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengajukan izin: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Leave $leave)
    {
        $leave->load(['user:id,name,email', 'user.role:id,role_name', 'approver:id,name']);
        
        return view('admin.leaves.show', compact('leave'));
    }

    public function viewEvidence(Leave $leave)
    {
        // Check if user has permission to view evidence
        $user = auth()->user();
        if (!$user->hasRole('Admin') && !$user->hasRole('Kepala Sekolah') && !$user->hasRole('Waka Kurikulum') && $leave->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (!$leave->evidence_path) {
            abort(404, 'Evidence not found');
        }

        $filePath = storage_path('app/public/' . $leave->evidence_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Evidence file not found');
        }

        $mimeType = mime_content_type($filePath);
        $fileName = $leave->evidence_original_name ?: 'evidence';

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function approve(Request $request, Leave $leave)
    {
        // Check if user has permission to approve
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Kepala Sekolah') && !auth()->user()->hasRole('Waka Kurikulum')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyetujui izin.');
        }

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
        // Check if user has permission to reject
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Kepala Sekolah') && !auth()->user()->hasRole('Waka Kurikulum')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menolak izin.');
        }

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
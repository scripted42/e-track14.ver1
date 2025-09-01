import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../providers/leave_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/leave.dart';

class LeaveListScreen extends StatefulWidget {
  const LeaveListScreen({super.key});

  @override
  State<LeaveListScreen> createState() => _LeaveListScreenState();
}

class _LeaveListScreenState extends State<LeaveListScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  String? selectedStatus;
  String? selectedLeaveType;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthProvider>().user;
    _tabController = TabController(
      length: user?.canApproveLeaves == true ? 2 : 1,
      vsync: this,
    );
    _loadLeaves();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  void _loadLeaves() {
    final provider = context.read<LeaveProvider>();
    provider.loadLeaves(
      status: selectedStatus,
      leaveType: selectedLeaveType,
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Manajemen Izin'),
        bottom: user?.canApproveLeaves == true
            ? TabBar(
                controller: _tabController,
                tabs: const [
                  Tab(text: 'Izin Saya'),
                  Tab(text: 'Persetujuan'),
                ],
              )
            : null,
        actions: [
          IconButton(
            onPressed: _showFilterDialog,
            icon: const Icon(Icons.filter_list),
          ),
        ],
      ),
      body: user?.canApproveLeaves == true
          ? TabBarView(
              controller: _tabController,
              children: [
                _buildMyLeavesTab(),
                _buildApprovalTab(),
              ],
            )
          : _buildMyLeavesTab(),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.go('/leave/request'),
        child: const Icon(Icons.add),
        tooltip: 'Ajukan Izin',
      ),
    );
  }

  Widget _buildMyLeavesTab() {
    return Consumer<LeaveProvider>(
      builder: (context, provider, child) {
        if (provider.isLoading) {
          return const Center(child: CircularProgressIndicator());
        }

        if (provider.error != null) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  'Error: ${provider.error}',
                  style: const TextStyle(color: Colors.red),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadLeaves,
                  child: const Text('Coba Lagi'),
                ),
              ],
            ),
          );
        }

        final myLeaves = provider.leaves.where((leave) {
          final user = context.read<AuthProvider>().user;
          return leave.userId == user?.id;
        }).toList();

        if (myLeaves.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.event_busy,
                  size: 64,
                  color: Colors.grey,
                ),
                const SizedBox(height: 16),
                const Text(
                  'Belum ada pengajuan izin',
                  style: TextStyle(fontSize: 16, color: Colors.grey),
                ),
                const SizedBox(height: 24),
                ElevatedButton.icon(
                  onPressed: () => context.go('/leave/request'),
                  icon: const Icon(Icons.add),
                  label: const Text('Ajukan Izin'),
                ),
              ],
            ),
          );
        }

        return RefreshIndicator(
          onRefresh: () async => _loadLeaves(),
          child: ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: myLeaves.length,
            separatorBuilder: (context, index) => const SizedBox(height: 8),
            itemBuilder: (context, index) {
              final leave = myLeaves[index];
              return _buildLeaveCard(leave);
            },
          ),
        );
      },
    );
  }

  Widget _buildApprovalTab() {
    return Consumer<LeaveProvider>(
      builder: (context, provider, child) {
        if (provider.isLoading) {
          return const Center(child: CircularProgressIndicator());
        }

        if (provider.error != null) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  'Error: ${provider.error}',
                  style: const TextStyle(color: Colors.red),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadLeaves,
                  child: const Text('Coba Lagi'),
                ),
              ],
            ),
          );
        }

        if (provider.pendingApprovals.isEmpty) {
          return const Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  Icons.check_circle_outline,
                  size: 64,
                  color: Colors.grey,
                ),
                SizedBox(height: 16),
                Text(
                  'Tidak ada izin yang menunggu persetujuan',
                  style: TextStyle(fontSize: 16, color: Colors.grey),
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          );
        }

        return RefreshIndicator(
          onRefresh: () async => _loadLeaves(),
          child: ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: provider.pendingApprovals.length,
            separatorBuilder: (context, index) => const SizedBox(height: 8),
            itemBuilder: (context, index) {
              final leave = provider.pendingApprovals[index];
              return _buildApprovalCard(leave);
            },
          ),
        );
      },
    );
  }

  Widget _buildLeaveCard(Leave leave) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    leave.leaveTypeLabel,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                _buildStatusChip(leave.status),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              '${DateFormat('dd/MM/yyyy').format(leave.startDate)} - ${DateFormat('dd/MM/yyyy').format(leave.endDate)}',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
            ),
            Text(
              '${leave.durationDays} hari',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
            ),
            const SizedBox(height: 8),
            if (leave.reason != null)
              Text(
                leave.reason!,
                style: const TextStyle(fontSize: 14),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            const SizedBox(height: 8),
            Row(
              children: [
                Text(
                  'Diajukan: ${DateFormat('dd/MM/yyyy HH:mm').format(leave.createdAt)}',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[500],
                  ),
                ),
                const Spacer(),
                if (leave.approvedAt != null)
                  Text(
                    'Disetujui: ${DateFormat('dd/MM/yyyy').format(leave.approvedAt!)}',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.grey[500],
                    ),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildApprovalCard(Leave leave) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        leave.user?.name ?? 'Unknown User',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      Text(
                        leave.leaveTypeLabel,
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                ),
                _buildStatusChip(leave.status),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              '${DateFormat('dd/MM/yyyy').format(leave.startDate)} - ${DateFormat('dd/MM/yyyy').format(leave.endDate)}',
              style: const TextStyle(fontSize: 14),
            ),
            Text(
              '${leave.durationDays} hari',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
            ),
            const SizedBox(height: 8),
            if (leave.reason != null)
              Text(
                leave.reason!,
                style: const TextStyle(fontSize: 14),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _rejectLeave(leave),
                    icon: const Icon(Icons.close, size: 16),
                    label: const Text('Tolak'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: Colors.red,
                      side: const BorderSide(color: Colors.red),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => _approveLeave(leave),
                    icon: const Icon(Icons.check, size: 16),
                    label: const Text('Setujui'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusChip(String status) {
    Color color;
    IconData icon;

    switch (status) {
      case 'disetujui':
        color = Colors.green;
        icon = Icons.check_circle;
        break;
      case 'ditolak':
        color = Colors.red;
        icon = Icons.cancel;
        break;
      default: // menunggu
        color = Colors.orange;
        icon = Icons.schedule;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: color),
          const SizedBox(width: 4),
          Text(
            _getStatusLabel(status),
            style: TextStyle(
              color: color,
              fontWeight: FontWeight.w500,
              fontSize: 12,
            ),
          ),
        ],
      ),
    );
  }

  String _getStatusLabel(String status) {
    switch (status) {
      case 'menunggu':
        return 'Menunggu';
      case 'disetujui':
        return 'Disetujui';
      case 'ditolak':
        return 'Ditolak';
      default:
        return status;
    }
  }

  Future<void> _approveLeave(Leave leave) async {
    final result = await showDialog<bool>(
      context: context,
      builder: (context) => _buildApprovalDialog(
        leave,
        'Setujui Izin',
        'Apakah Anda yakin ingin menyetujui pengajuan izin ini?',
        Colors.green,
      ),
    );

    if (result == true) {
      final provider = context.read<LeaveProvider>();
      final success = await provider.approveLeave(leave.id);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              success ? 'Izin berhasil disetujui' : 'Gagal menyetujui izin',
            ),
            backgroundColor: success ? Colors.green : Colors.red,
          ),
        );
      }
    }
  }

  Future<void> _rejectLeave(Leave leave) async {
    final result = await showDialog<String>(
      context: context,
      builder: (context) => _buildRejectionDialog(leave),
    );

    if (result != null && result.isNotEmpty) {
      final provider = context.read<LeaveProvider>();
      final success = await provider.rejectLeave(leave.id, comment: result);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              success ? 'Izin berhasil ditolak' : 'Gagal menolak izin',
            ),
            backgroundColor: success ? Colors.orange : Colors.red,
          ),
        );
      }
    }
  }

  Widget _buildApprovalDialog(
    Leave leave,
    String title,
    String message,
    Color color,
  ) {
    return AlertDialog(
      title: Text(title),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(message),
          const SizedBox(height: 16),
          Text(
            'Pemohon: ${leave.user?.name}',
            style: const TextStyle(fontWeight: FontWeight.w500),
          ),
          Text('Jenis: ${leave.leaveTypeLabel}'),
          Text(
            'Periode: ${DateFormat('dd/MM/yyyy').format(leave.startDate)} - ${DateFormat('dd/MM/yyyy').format(leave.endDate)}',
          ),
          Text('Durasi: ${leave.durationDays} hari'),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(false),
          child: const Text('Batal'),
        ),
        ElevatedButton(
          onPressed: () => Navigator.of(context).pop(true),
          style: ElevatedButton.styleFrom(backgroundColor: color),
          child: const Text('Konfirmasi'),
        ),
      ],
    );
  }

  Widget _buildRejectionDialog(Leave leave) {
    final commentController = TextEditingController();

    return AlertDialog(
      title: const Text('Tolak Izin'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Pemohon: ${leave.user?.name}',
            style: const TextStyle(fontWeight: FontWeight.w500),
          ),
          Text('Jenis: ${leave.leaveTypeLabel}'),
          Text(
            'Periode: ${DateFormat('dd/MM/yyyy').format(leave.startDate)} - ${DateFormat('dd/MM/yyyy').format(leave.endDate)}',
          ),
          const SizedBox(height: 16),
          TextField(
            controller: commentController,
            decoration: const InputDecoration(
              labelText: 'Alasan Penolakan *',
              hintText: 'Masukkan alasan penolakan...',
              border: OutlineInputBorder(),
            ),
            maxLines: 3,
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Batal'),
        ),
        ElevatedButton(
          onPressed: () {
            if (commentController.text.trim().isNotEmpty) {
              Navigator.of(context).pop(commentController.text.trim());
            }
          },
          style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
          child: const Text('Tolak'),
        ),
      ],
    );
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Filter Izin'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            DropdownButtonFormField<String>(
              value: selectedStatus,
              decoration: const InputDecoration(
                labelText: 'Status',
                border: OutlineInputBorder(),
              ),
              items: const [
                DropdownMenuItem(value: null, child: Text('Semua Status')),
                DropdownMenuItem(value: 'menunggu', child: Text('Menunggu')),
                DropdownMenuItem(value: 'disetujui', child: Text('Disetujui')),
                DropdownMenuItem(value: 'ditolak', child: Text('Ditolak')),
              ],
              onChanged: (value) {
                setState(() {
                  selectedStatus = value;
                });
              },
            ),
            const SizedBox(height: 16),
            DropdownButtonFormField<String>(
              value: selectedLeaveType,
              decoration: const InputDecoration(
                labelText: 'Jenis Izin',
                border: OutlineInputBorder(),
              ),
              items: const [
                DropdownMenuItem(value: null, child: Text('Semua Jenis')),
                DropdownMenuItem(value: 'izin', child: Text('Izin')),
                DropdownMenuItem(value: 'sakit', child: Text('Sakit')),
                DropdownMenuItem(value: 'cuti', child: Text('Cuti')),
                DropdownMenuItem(value: 'dinas_luar', child: Text('Dinas Luar')),
              ],
              onChanged: (value) {
                setState(() {
                  selectedLeaveType = value;
                });
              },
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.of(context).pop();
              _loadLeaves();
            },
            child: const Text('Terapkan'),
          ),
        ],
      ),
    );
  }
}
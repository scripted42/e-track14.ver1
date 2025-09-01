import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/leave_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/leave.dart';

class LeaveApprovalScreen extends StatefulWidget {
  const LeaveApprovalScreen({super.key});

  @override
  State<LeaveApprovalScreen> createState() => _LeaveApprovalScreenState();
}

class _LeaveApprovalScreenState extends State<LeaveApprovalScreen> {
  String selectedFilter = 'pending';

  @override
  void initState() {
    super.initState();
    _loadLeaves();
  }

  void _loadLeaves() {
    final provider = context.read<LeaveProvider>();
    provider.loadLeaves(
      status: selectedFilter == 'all' ? null : 'menunggu',
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    if (user?.canApproveLeaves != true) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Persetujuan Izin'),
        ),
        body: const Center(
          child: Text(
            'Anda tidak memiliki akses untuk menyetujui izin',
            style: TextStyle(fontSize: 16),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Persetujuan Izin'),
        actions: [
          PopupMenuButton<String>(
            initialValue: selectedFilter,
            onSelected: (value) {
              setState(() {
                selectedFilter = value;
              });
              _loadLeaves();
            },
            itemBuilder: (context) => [
              const PopupMenuItem(
                value: 'pending',
                child: Text('Menunggu Persetujuan'),
              ),
              const PopupMenuItem(
                value: 'all',
                child: Text('Semua Izin'),
              ),
            ],
            child: const Icon(Icons.filter_list),
          ),
        ],
      ),
      body: Consumer<LeaveProvider>(
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

          final filteredLeaves = selectedFilter == 'pending'
              ? provider.pendingApprovals
              : provider.leaves;

          if (filteredLeaves.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    selectedFilter == 'pending'
                        ? Icons.check_circle_outline
                        : Icons.event_busy,
                    size: 64,
                    color: Colors.grey,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    selectedFilter == 'pending'
                        ? 'Tidak ada izin yang menunggu persetujuan'
                        : 'Belum ada data izin',
                    style: const TextStyle(fontSize: 16, color: Colors.grey),
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
              itemCount: filteredLeaves.length,
              separatorBuilder: (context, index) => const SizedBox(height: 12),
              itemBuilder: (context, index) {
                final leave = filteredLeaves[index];
                return _buildLeaveCard(leave);
              },
            ),
          );
        },
      ),
    );
  }

  Widget _buildLeaveCard(Leave leave) {
    return Card(
      elevation: 2,
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
                          fontSize: 18,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      Text(
                        leave.user?.role ?? '',
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
            const SizedBox(height: 16),
            _buildInfoRow(
              Icons.category,
              'Jenis Izin',
              leave.leaveTypeLabel,
              _getLeaveTypeColor(leave.leaveType),
            ),
            const SizedBox(height: 8),
            _buildInfoRow(
              Icons.date_range,
              'Periode',
              '${DateFormat('dd MMM yyyy').format(leave.startDate)} - ${DateFormat('dd MMM yyyy').format(leave.endDate)}',
              Colors.blue,
            ),
            const SizedBox(height: 8),
            _buildInfoRow(
              Icons.schedule,
              'Durasi',
              '${leave.durationDays} hari',
              Colors.orange,
            ),
            const SizedBox(height: 8),
            _buildInfoRow(
              Icons.access_time,
              'Diajukan',
              DateFormat('dd MMM yyyy, HH:mm').format(leave.createdAt),
              Colors.grey,
            ),
            if (leave.reason != null) ...[
              const SizedBox(height: 16),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.grey[50],
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.grey[200]!),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Alasan:',
                      style: TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 14,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      leave.reason!,
                      style: const TextStyle(fontSize: 14),
                    ),
                  ],
                ),
              ),
            ],
            if (leave.attachmentPath != null) ...[
              const SizedBox(height: 12),
              Row(
                children: [
                  Icon(
                    Icons.attachment,
                    size: 16,
                    color: Colors.blue[600],
                  ),
                  const SizedBox(width: 4),
                  Text(
                    'Lampiran tersedia',
                    style: TextStyle(
                      color: Colors.blue[600],
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ],
            if (leave.isPending) ...[
              const SizedBox(height: 16),
              const Divider(),
              const SizedBox(height: 8),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () => _showRejectDialog(leave),
                      icon: const Icon(Icons.close, size: 18),
                      label: const Text('Tolak'),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: Colors.red,
                        side: const BorderSide(color: Colors.red),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () => _showApproveDialog(leave),
                      icon: const Icon(Icons.check, size: 18),
                      label: const Text('Setujui'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],
              ),
            ] else if (leave.approvedAt != null) ...[
              const SizedBox(height: 16),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: leave.isApproved
                      ? Colors.green[50]
                      : Colors.red[50],
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(
                    color: leave.isApproved
                        ? Colors.green[200]!
                        : Colors.red[200]!,
                  ),
                ),
                child: Row(
                  children: [
                    Icon(
                      leave.isApproved ? Icons.check_circle : Icons.cancel,
                      color: leave.isApproved ? Colors.green : Colors.red,
                      size: 20,
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            leave.isApproved ? 'Disetujui oleh:' : 'Ditolak oleh:',
                            style: TextStyle(
                              fontSize: 12,
                              color: leave.isApproved
                                  ? Colors.green[700]
                                  : Colors.red[700],
                            ),
                          ),
                          Text(
                            leave.approver?.name ?? 'Unknown',
                            style: TextStyle(
                              fontWeight: FontWeight.w600,
                              color: leave.isApproved
                                  ? Colors.green[700]
                                  : Colors.red[700],
                            ),
                          ),
                          Text(
                            DateFormat('dd MMM yyyy, HH:mm')
                                .format(leave.approvedAt!),
                            style: TextStyle(
                              fontSize: 12,
                              color: leave.isApproved
                                  ? Colors.green[600]
                                  : Colors.red[600],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(
    IconData icon,
    String label,
    String value,
    Color color,
  ) {
    return Row(
      children: [
        Icon(icon, size: 16, color: color),
        const SizedBox(width: 8),
        Text(
          '$label:',
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey[600],
          ),
        ),
        const SizedBox(width: 4),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStatusChip(String status) {
    Color color;
    String label;
    IconData icon;

    switch (status) {
      case 'disetujui':
        color = Colors.green;
        label = 'Disetujui';
        icon = Icons.check_circle;
        break;
      case 'ditolak':
        color = Colors.red;
        label = 'Ditolak';
        icon = Icons.cancel;
        break;
      default:
        color = Colors.orange;
        label = 'Menunggu';
        icon = Icons.schedule;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              color: color,
              fontWeight: FontWeight.w600,
              fontSize: 12,
            ),
          ),
        ],
      ),
    );
  }

  Color _getLeaveTypeColor(String type) {
    switch (type) {
      case 'izin':
        return Colors.blue;
      case 'sakit':
        return Colors.red;
      case 'cuti':
        return Colors.green;
      case 'dinas_luar':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }

  Future<void> _showApproveDialog(Leave leave) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Setujui Izin'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Apakah Anda yakin ingin menyetujui pengajuan izin ini?'),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: BorderRadius.circular(8),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Pemohon: ${leave.user?.name}'),
                  Text('Jenis: ${leave.leaveTypeLabel}'),
                  Text(
                    'Periode: ${DateFormat('dd/MM/yyyy').format(leave.startDate)} - ${DateFormat('dd/MM/yyyy').format(leave.endDate)}',
                  ),
                  Text('Durasi: ${leave.durationDays} hari'),
                ],
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.of(context).pop(true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
            child: const Text('Setujui'),
          ),
        ],
      ),
    );

    if (confirmed == true) {
      await _approveLeave(leave);
    }
  }

  Future<void> _showRejectDialog(Leave leave) async {
    final commentController = TextEditingController();

    final comment = await showDialog<String>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Tolak Izin'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: BorderRadius.circular(8),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Pemohon: ${leave.user?.name}'),
                  Text('Jenis: ${leave.leaveTypeLabel}'),
                  Text(
                    'Periode: ${DateFormat('dd/MM/yyyy').format(leave.startDate)} - ${DateFormat('dd/MM/yyyy').format(leave.endDate)}',
                  ),
                ],
              ),
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
              autofocus: true,
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
              final text = commentController.text.trim();
              if (text.isNotEmpty) {
                Navigator.of(context).pop(text);
              }
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Tolak'),
          ),
        ],
      ),
    );

    if (comment != null && comment.isNotEmpty) {
      await _rejectLeave(leave, comment);
    }
  }

  Future<void> _approveLeave(Leave leave) async {
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

  Future<void> _rejectLeave(Leave leave, String comment) async {
    final provider = context.read<LeaveProvider>();
    final success = await provider.rejectLeave(leave.id, comment: comment);

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
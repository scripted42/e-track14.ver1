import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../providers/student_attendance_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/student.dart';
import '../../widgets/loading_button.dart';

class StudentAttendanceScreen extends StatefulWidget {
  const StudentAttendanceScreen({super.key});

  @override
  State<StudentAttendanceScreen> createState() => _StudentAttendanceScreenState();
}

class _StudentAttendanceScreenState extends State<StudentAttendanceScreen> {
  String? selectedClass;
  DateTime selectedDate = DateTime.now();
  String? selectedStatus;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  void _loadInitialData() {
    final provider = context.read<StudentAttendanceProvider>();
    provider.loadClasses();
    provider.loadAttendance(date: selectedDate);
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    
    if (user?.canMarkStudentAttendance != true) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Absensi Siswa'),
        ),
        body: const Center(
          child: Text(
            'Anda tidak memiliki akses untuk mengelola absensi siswa',
            style: TextStyle(fontSize: 16),
            textAlign: TextAlign.center,
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Absensi Siswa'),
        actions: [
          IconButton(
            onPressed: () => context.go('/student-attendance/scan'),
            icon: const Icon(Icons.qr_code_scanner),
            tooltip: 'Scan QR Siswa',
          ),
        ],
      ),
      body: Column(
        children: [
          _buildFilters(),
          Expanded(child: _buildAttendanceList()),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.go('/student-attendance/scan'),
        child: const Icon(Icons.qr_code_scanner),
        tooltip: 'Scan QR Siswa',
      ),
    );
  }

  Widget _buildFilters() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            spreadRadius: 1,
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: _buildClassFilter(),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _buildStatusFilter(),
              ),
            ],
          ),
          const SizedBox(height: 12),
          _buildDateSelector(),
        ],
      ),
    );
  }

  Widget _buildClassFilter() {
    return Consumer<StudentAttendanceProvider>(
      builder: (context, provider, child) {
        return DropdownButtonFormField<String>(
          value: selectedClass,
          decoration: const InputDecoration(
            labelText: 'Kelas',
            border: OutlineInputBorder(),
            contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
          ),
          items: [
            const DropdownMenuItem<String>(
              value: null,
              child: Text('Semua Kelas'),
            ),
            ...provider.classes.map((className) {
              return DropdownMenuItem<String>(
                value: className,
                child: Text(className),
              );
            }),
          ],
          onChanged: (value) {
            setState(() {
              selectedClass = value;
            });
            _filterAttendance();
          },
        );
      },
    );
  }

  Widget _buildStatusFilter() {
    return DropdownButtonFormField<String>(
      value: selectedStatus,
      decoration: const InputDecoration(
        labelText: 'Status',
        border: OutlineInputBorder(),
        contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      ),
      items: const [
        DropdownMenuItem<String>(
          value: null,
          child: Text('Semua Status'),
        ),
        DropdownMenuItem<String>(
          value: 'hadir',
          child: Text('Hadir'),
        ),
        DropdownMenuItem<String>(
          value: 'izin',
          child: Text('Izin'),
        ),
        DropdownMenuItem<String>(
          value: 'sakit',
          child: Text('Sakit'),
        ),
        DropdownMenuItem<String>(
          value: 'alpha',
          child: Text('Alpha'),
        ),
      ],
      onChanged: (value) {
        setState(() {
          selectedStatus = value;
        });
        _filterAttendance();
      },
    );
  }

  Widget _buildDateSelector() {
    return InkWell(
      onTap: _selectDate,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 16),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey[300]!),
          borderRadius: BorderRadius.circular(4),
        ),
        child: Row(
          children: [
            const Icon(Icons.calendar_today, size: 20),
            const SizedBox(width: 8),
            Text(
              'Tanggal: ${DateFormat('dd/MM/yyyy').format(selectedDate)}',
              style: const TextStyle(fontSize: 16),
            ),
            const Spacer(),
            const Icon(Icons.arrow_drop_down),
          ],
        ),
      ),
    );
  }

  Widget _buildAttendanceList() {
    return Consumer<StudentAttendanceProvider>(
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
                  onPressed: () => _filterAttendance(),
                  child: const Text('Coba Lagi'),
                ),
              ],
            ),
          );
        }

        if (provider.attendance.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.school_outlined,
                  size: 64,
                  color: Colors.grey,
                ),
                const SizedBox(height: 16),
                const Text(
                  'Belum ada data absensi siswa',
                  style: TextStyle(fontSize: 16, color: Colors.grey),
                ),
                const SizedBox(height: 24),
                ElevatedButton.icon(
                  onPressed: () => context.go('/student-attendance/scan'),
                  icon: const Icon(Icons.qr_code_scanner),
                  label: const Text('Mulai Absen Siswa'),
                ),
              ],
            ),
          );
        }

        return RefreshIndicator(
          onRefresh: () async => _filterAttendance(),
          child: ListView.separated(
            padding: const EdgeInsets.all(16),
            itemCount: provider.attendance.length,
            separatorBuilder: (context, index) => const SizedBox(height: 8),
            itemBuilder: (context, index) {
              final attendance = provider.attendance[index];
              return _buildAttendanceCard(attendance);
            },
          ),
        );
      },
    );
  }

  Widget _buildAttendanceCard(StudentAttendance attendance) {
    return Card(
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: _getStatusColor(attendance.status),
          child: Text(
            attendance.student?.name.substring(0, 1).toUpperCase() ?? 'S',
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
        title: Text(
          attendance.student?.name ?? 'Unknown Student',
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Kelas: ${attendance.student?.className ?? '-'}'),
            Text('Guru: ${attendance.teacher?.name ?? '-'}'),
            Text(
              'Waktu: ${DateFormat('HH:mm').format(attendance.createdAt)}',
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
        trailing: Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: _getStatusColor(attendance.status).withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: _getStatusColor(attendance.status).withOpacity(0.3),
            ),
          ),
          child: Text(
            attendance.statusLabel,
            style: TextStyle(
              color: _getStatusColor(attendance.status),
              fontWeight: FontWeight.w500,
              fontSize: 12,
            ),
          ),
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'hadir':
        return Colors.green;
      case 'izin':
        return Colors.blue;
      case 'sakit':
        return Colors.orange;
      case 'alpha':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  Future<void> _selectDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: selectedDate,
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now(),
    );

    if (picked != null && picked != selectedDate) {
      setState(() {
        selectedDate = picked;
      });
      _filterAttendance();
    }
  }

  void _filterAttendance() {
    final provider = context.read<StudentAttendanceProvider>();
    provider.loadAttendance(
      className: selectedClass,
      date: selectedDate,
      status: selectedStatus,
    );
  }
}
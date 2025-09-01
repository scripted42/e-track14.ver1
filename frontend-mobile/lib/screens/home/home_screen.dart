import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:go_router/go_router.dart';
import '../../providers/auth_provider.dart';
import '../../providers/attendance_provider.dart';
import '../../providers/leave_provider.dart';
import '../../providers/student_attendance_provider.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  void _loadInitialData() {
    final attendanceProvider = context.read<AttendanceProvider>();
    final leaveProvider = context.read<LeaveProvider>();
    final studentAttendanceProvider = context.read<StudentAttendanceProvider>();

    attendanceProvider.loadTodayAttendance();
    attendanceProvider.loadSettings();
    leaveProvider.loadLeaves();
    
    // Load student attendance classes if user is teacher
    final user = context.read<AuthProvider>().user;
    if (user?.canMarkStudentAttendance == true) {
      studentAttendanceProvider.loadClasses();
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: [
          _buildDashboard(),
          _buildAttendanceTab(),
          if (user?.canMarkStudentAttendance == true) _buildStudentTab(),
          _buildLeaveTab(),
          _buildProfileTab(),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) => setState(() => _currentIndex = index),
        type: BottomNavigationBarType.fixed,
        items: [
          const BottomNavigationBarItem(
            icon: Icon(Icons.dashboard),
            label: 'Dashboard',
          ),
          const BottomNavigationBarItem(
            icon: Icon(Icons.access_time),
            label: 'Absensi',
          ),
          if (user?.canMarkStudentAttendance == true)
            const BottomNavigationBarItem(
              icon: Icon(Icons.school),
              label: 'Siswa',
            ),
          const BottomNavigationBarItem(
            icon: Icon(Icons.event_busy),
            label: 'Izin',
          ),
          const BottomNavigationBarItem(
            icon: Icon(Icons.person),
            label: 'Profil',
          ),
        ],
      ),
    );
  }

  Widget _buildDashboard() {
    return SafeArea(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildHeader(),
            const SizedBox(height: 24),
            _buildTodayAttendanceCard(),
            const SizedBox(height: 16),
            _buildQuickActions(),
            const SizedBox(height: 16),
            _buildRecentActivity(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, child) {
        final user = authProvider.user;
        return Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Selamat datang,',
                    style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    user?.name ?? 'User',
                    style: Theme.of(context).textTheme.headlineLarge?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  Text(
                    user?.role ?? '',
                    style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                      color: Theme.of(context).colorScheme.primary,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
            IconButton(
              onPressed: () => authProvider.logout(),
              icon: const Icon(Icons.logout),
              tooltip: 'Logout',
            ),
          ],
        );
      },
    );
  }

  Widget _buildTodayAttendanceCard() {
    return Consumer<AttendanceProvider>(
      builder: (context, attendanceProvider, child) {
        final checkin = attendanceProvider.todayCheckin;
        final checkout = attendanceProvider.todayCheckout;

        return Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Absensi Hari Ini',
                  style: Theme.of(context).textTheme.headlineSmall,
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: _buildAttendanceStatus(
                        'Check In',
                        checkin?.timestamp,
                        checkin?.statusLabel,
                        Icons.login,
                        Colors.green,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: _buildAttendanceStatus(
                        'Check Out',
                        checkout?.timestamp,
                        checkout?.statusLabel,
                        Icons.logout,
                        Colors.orange,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildAttendanceStatus(
    String title,
    DateTime? time,
    String? status,
    IconData icon,
    Color color,
  ) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 16, color: color),
            const SizedBox(width: 8),
            Text(
              title,
              style: Theme.of(context).textTheme.titleSmall?.copyWith(
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Text(
          time != null
              ? '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}'
              : '-',
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        if (status != null)
          Text(
            status,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: color,
              fontWeight: FontWeight.w500,
            ),
          ),
      ],
    );
  }

  Widget _buildQuickActions() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Aksi Cepat',
          style: Theme.of(context).textTheme.headlineSmall,
        ),
        const SizedBox(height: 16),
        Consumer<AttendanceProvider>(
          builder: (context, attendanceProvider, child) {
            return Row(
              children: [
                Expanded(
                  child: _buildActionCard(
                    'Check In',
                    Icons.login,
                    Colors.green,
                    attendanceProvider.hasCheckedInToday
                        ? null
                        : () => context.go('/attendance/checkin'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildActionCard(
                    'Check Out',
                    Icons.logout,
                    Colors.orange,
                    attendanceProvider.canCheckOut
                        ? () => context.go('/attendance/checkout')
                        : null,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildActionCard(
                    'Ajukan Izin',
                    Icons.event_busy,
                    Colors.blue,
                    () => context.go('/leave/request'),
                  ),
                ),
              ],
            );
          },
        ),
      ],
    );
  }

  Widget _buildActionCard(
    String title,
    IconData icon,
    Color color,
    VoidCallback? onTap,
  ) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: onTap != null ? color.withOpacity(0.1) : Colors.grey[100],
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: onTap != null ? color.withOpacity(0.3) : Colors.grey[300]!,
          ),
        ),
        child: Column(
          children: [
            Icon(
              icon,
              size: 32,
              color: onTap != null ? color : Colors.grey,
            ),
            const SizedBox(height: 8),
            Text(
              title,
              style: Theme.of(context).textTheme.titleSmall?.copyWith(
                color: onTap != null ? color : Colors.grey,
                fontWeight: FontWeight.w500,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRecentActivity() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'Aktivitas Terakhir',
              style: Theme.of(context).textTheme.headlineSmall,
            ),
            TextButton(
              onPressed: () => context.go('/attendance/history'),
              child: const Text('Lihat Semua'),
            ),
          ],
        ),
        const SizedBox(height: 16),
        Consumer<AttendanceProvider>(
          builder: (context, attendanceProvider, child) {
            if (attendanceProvider.isLoading) {
              return const Center(child: CircularProgressIndicator());
            }

            if (attendanceProvider.attendance.isEmpty) {
              return const Center(
                child: Text('Belum ada aktivitas'),
              );
            }

            return ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: attendanceProvider.attendance.take(3).length,
              itemBuilder: (context, index) {
                final attendance = attendanceProvider.attendance[index];
                return ListTile(
                  leading: Icon(
                    attendance.isCheckIn ? Icons.login : Icons.logout,
                    color: attendance.isCheckIn ? Colors.green : Colors.orange,
                  ),
                  title: Text(attendance.typeLabel),
                  subtitle: Text(attendance.statusLabel),
                  trailing: Text(
                    '${attendance.timestamp.day}/${attendance.timestamp.month}',
                  ),
                );
              },
            );
          },
        ),
      ],
    );
  }

  Widget _buildAttendanceTab() {
    return const Center(child: Text('Attendance Tab - To be implemented'));
  }

  Widget _buildStudentTab() {
    return const Center(child: Text('Student Tab - To be implemented'));
  }

  Widget _buildLeaveTab() {
    return const Center(child: Text('Leave Tab - To be implemented'));
  }

  Widget _buildProfileTab() {
    return const Center(child: Text('Profile Tab - To be implemented'));
  }
}
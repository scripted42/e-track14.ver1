import 'package:go_router/go_router.dart';
import 'package:flutter/material.dart';
import '../providers/auth_provider.dart';
import '../screens/auth/login_screen.dart';
import '../screens/home/home_screen.dart';
import '../screens/attendance/attendance_screen.dart';
import '../screens/attendance/checkin_screen.dart';
import '../screens/attendance/checkout_screen.dart';
import '../screens/attendance/attendance_history_screen.dart';
import '../screens/leave/leave_list_screen.dart';
import '../screens/leave/leave_request_screen.dart';
import '../screens/leave/leave_approval_screen.dart';
import '../screens/student/student_attendance_screen.dart';
import '../screens/student/student_scan_screen.dart';
import '../screens/profile/profile_screen.dart';
import '../screens/splash/splash_screen.dart';

class AppRouter {
  static GoRouter router(AuthProvider authProvider) {
    return GoRouter(
      initialLocation: '/splash',
      redirect: (context, state) {
        final isAuthenticated = authProvider.isAuthenticated;
        final isLoading = authProvider.isLoading;
        
        // Show splash while loading
        if (isLoading) {
          return '/splash';
        }
        
        // Redirect to login if not authenticated
        if (!isAuthenticated && state.matchedLocation != '/login') {
          return '/login';
        }
        
        // Redirect to home if authenticated and on login/splash
        if (isAuthenticated && 
            (state.matchedLocation == '/login' || state.matchedLocation == '/splash')) {
          return '/home';
        }
        
        return null;
      },
      routes: [
        GoRoute(
          path: '/splash',
          builder: (context, state) => const SplashScreen(),
        ),
        GoRoute(
          path: '/login',
          builder: (context, state) => const LoginScreen(),
        ),
        GoRoute(
          path: '/home',
          builder: (context, state) => const HomeScreen(),
        ),
        GoRoute(
          path: '/attendance',
          builder: (context, state) => const AttendanceScreen(),
          routes: [
            GoRoute(
              path: 'checkin',
              builder: (context, state) => const CheckinScreen(),
            ),
            GoRoute(
              path: 'checkout',
              builder: (context, state) => const CheckoutScreen(),
            ),
            GoRoute(
              path: 'history',
              builder: (context, state) => const AttendanceHistoryScreen(),
            ),
          ],
        ),
        GoRoute(
          path: '/leave',
          builder: (context, state) => const LeaveListScreen(),
          routes: [
            GoRoute(
              path: 'request',
              builder: (context, state) => const LeaveRequestScreen(),
            ),
            GoRoute(
              path: 'approval',
              builder: (context, state) => const LeaveApprovalScreen(),
            ),
          ],
        ),
        GoRoute(
          path: '/student-attendance',
          builder: (context, state) => const StudentAttendanceScreen(),
          routes: [
            GoRoute(
              path: 'scan',
              builder: (context, state) => const StudentScanScreen(),
            ),
          ],
        ),
        GoRoute(
          path: '/profile',
          builder: (context, state) => const ProfileScreen(),
        ),
      ],
    );
  }
}
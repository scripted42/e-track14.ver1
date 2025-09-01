import 'package:flutter/foundation.dart';
import '../models/student.dart';
import '../services/api_service.dart';

class StudentAttendanceProvider with ChangeNotifier {
  final ApiService _apiService;
  
  List<StudentAttendance> _attendance = [];
  List<Student> _students = [];
  List<String> _classes = [];
  bool _isLoading = false;
  String? _error;
  
  StudentAttendanceProvider(this._apiService);
  
  List<StudentAttendance> get attendance => _attendance;
  List<Student> get students => _students;
  List<String> get classes => _classes;
  bool get isLoading => _isLoading;
  String? get error => _error;
  
  Future<void> loadAttendance({
    String? className,
    DateTime? date,
    String? status,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final queryParams = <String, String>{};
      if (className != null) queryParams['class_name'] = className;
      if (date != null) {
        queryParams['date'] = date.toIso8601String().split('T')[0];
      }
      if (status != null) queryParams['status'] = status;
      
      final uri = Uri(
        path: '/student-attendance',
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );
      
      final response = await _apiService.get(uri.toString());
      
      if (response.success && response.data != null) {
        final List<dynamic> attendanceData = response.data['data'];
        _attendance = attendanceData
            .map((json) => StudentAttendance.fromJson(json))
            .toList();
      }
    } catch (e) {
      _setError('Failed to load student attendance: $e');
    }
    
    _setLoading(false);
  }
  
  Future<void> loadStudents({
    String? className,
    String? search,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final queryParams = <String, String>{};
      if (className != null) queryParams['class_name'] = className;
      if (search != null) queryParams['search'] = search;
      
      final uri = Uri(
        path: '/student-attendance/students',
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );
      
      final response = await _apiService.get(uri.toString());
      
      if (response.success && response.data != null) {
        final List<dynamic> studentData = response.data['data'];
        _students = studentData
            .map((json) => Student.fromJson(json))
            .toList();
      }
    } catch (e) {
      _setError('Failed to load students: $e');
    }
    
    _setLoading(false);
  }
  
  Future<void> loadClasses() async {
    try {
      final response = await _apiService.get('/student-attendance/classes');
      
      if (response.success && response.data != null) {
        _classes = List<String>.from(response.data);
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Failed to load classes: $e');
    }
  }
  
  Future<bool> scanStudentQr({
    required String qrCode,
    String status = 'hadir',
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final response = await _apiService.post(
        '/student-attendance/scan',
        data: {
          'qr_code': qrCode,
          'status': status,
        },
      );
      
      if (response.success) {
        await loadAttendance(); // Reload attendance
        _setLoading(false);
        return true;
      } else {
        _setError(response.message ?? 'Failed to scan student QR');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Scan QR error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<StudentAttendanceResult?> scanStudent(String qrCode) async {
    try {
      final response = await _apiService.post(
        '/student-attendance/scan',
        data: {'qr_code': qrCode},
      );
      
      if (response.success && response.data != null) {
        return StudentAttendanceResult(
          student: Student.fromJson(response.data['student']),
          attendance: StudentAttendance.fromJson(response.data['attendance']),
          success: true,
          message: response.message,
        );
      } else {
        return StudentAttendanceResult(
          success: false,
          message: response.message ?? 'Unknown error',
        );
      }
    } catch (e) {
      return StudentAttendanceResult(
        success: false,
        message: 'Network error: $e',
      );
    }
  }
  
  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }
  
  void _setError(String? error) {
    _error = error;
    notifyListeners();
  }
  
  void clearError() {
    _error = null;
    notifyListeners();
  }
}

class StudentAttendanceResult {
  final Student? student;
  final StudentAttendance? attendance;
  final bool success;
  final String? message;
  
  StudentAttendanceResult({
    this.student,
    this.attendance,
    required this.success,
    this.message,
  });
}
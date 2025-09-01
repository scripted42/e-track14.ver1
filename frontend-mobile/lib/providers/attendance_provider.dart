import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:geolocator/geolocator.dart';
import 'package:permission_handler/permission_handler.dart';
import '../models/attendance.dart';
import '../models/settings.dart';
import '../services/api_service.dart';

class AttendanceProvider with ChangeNotifier {
  final ApiService _apiService;
  
  List<Attendance> _attendance = [];
  Attendance? _todayCheckin;
  Attendance? _todayCheckout;
  AppSettings? _settings;
  bool _isLoading = false;
  String? _error;
  
  AttendanceProvider(this._apiService);
  
  List<Attendance> get attendance => _attendance;
  Attendance? get todayCheckin => _todayCheckin;
  Attendance? get todayCheckout => _todayCheckout;
  AppSettings? get settings => _settings;
  bool get isLoading => _isLoading;
  String? get error => _error;
  
  bool get hasCheckedInToday => _todayCheckin != null;
  bool get hasCheckedOutToday => _todayCheckout != null;
  bool get canCheckOut => hasCheckedInToday && !hasCheckedOutToday;
  
  Future<void> loadTodayAttendance() async {
    _setLoading(true);
    _setError(null);
    
    try {
      final response = await _apiService.get('/attendance/today');
      
      if (response.success && response.data != null) {
        final data = response.data;
        _todayCheckin = data['checkin'] != null 
            ? Attendance.fromJson(data['checkin'])
            : null;
        _todayCheckout = data['checkout'] != null 
            ? Attendance.fromJson(data['checkout'])
            : null;
      }
    } catch (e) {
      _setError('Failed to load today\'s attendance: $e');
    }
    
    _setLoading(false);
  }
  
  Future<void> loadAttendanceHistory({
    DateTime? startDate,
    DateTime? endDate,
    String? status,
    String? type,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final queryParams = <String, String>{};
      if (startDate != null) {
        queryParams['start_date'] = startDate.toIso8601String().split('T')[0];
      }
      if (endDate != null) {
        queryParams['end_date'] = endDate.toIso8601String().split('T')[0];
      }
      if (status != null) {
        queryParams['status'] = status;
      }
      if (type != null) {
        queryParams['type'] = type;
      }
      
      final uri = Uri(
        path: '/attendance/history',
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );
      
      final response = await _apiService.get(uri.toString());
      
      if (response.success && response.data != null) {
        final List<dynamic> attendanceData = response.data['data'];
        _attendance = attendanceData
            .map((json) => Attendance.fromJson(json))
            .toList();
      }
    } catch (e) {
      _setError('Failed to load attendance history: $e');
    }
    
    _setLoading(false);
  }
  
  Future<void> loadSettings() async {
    try {
      final response = await _apiService.get('/settings/current');
      
      if (response.success && response.data != null) {
        _settings = AppSettings.fromJson(response.data);
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Failed to load settings: $e');
    }
  }
  
  Future<bool> checkIn({
    required File photoFile,
    required String qrCode,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      // Get current location
      final position = await _getCurrentPosition();
      if (position == null) {
        _setError('Failed to get current location');
        _setLoading(false);
        return false;
      }
      
      // Prepare multipart request
      final fields = <String, String>{
        'latitude': position.latitude.toString(),
        'longitude': position.longitude.toString(),
        'accuracy': position.accuracy.toString(),
        'qr_code': qrCode,
      };
      
      final files = [
        MultipartFile(
          field: 'photo',
          file: photoFile,
          filename: 'checkin_${DateTime.now().millisecondsSinceEpoch}.jpg',
          contentType: 'image/jpeg',
        ),
      ];
      
      final response = await _apiService.postMultipart(
        '/attendance/checkin',
        fields,
        files: files,
      );
      
      if (response.success && response.data != null) {
        _todayCheckin = Attendance.fromJson(response.data);
        _setLoading(false);
        notifyListeners();
        return true;
      } else {
        _setError(response.message ?? 'Check-in failed');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Check-in error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<bool> checkOut({
    required File photoFile,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      // Get current location
      final position = await _getCurrentPosition();
      if (position == null) {
        _setError('Failed to get current location');
        _setLoading(false);
        return false;
      }
      
      // Prepare multipart request
      final fields = <String, String>{
        'latitude': position.latitude.toString(),
        'longitude': position.longitude.toString(),
        'accuracy': position.accuracy.toString(),
      };
      
      final files = [
        MultipartFile(
          field: 'photo',
          file: photoFile,
          filename: 'checkout_${DateTime.now().millisecondsSinceEpoch}.jpg',
          contentType: 'image/jpeg',
        ),
      ];
      
      final response = await _apiService.postMultipart(
        '/attendance/checkout',
        fields,
        files: files,
      );
      
      if (response.success && response.data != null) {
        _todayCheckout = Attendance.fromJson(response.data);
        _setLoading(false);
        notifyListeners();
        return true;
      } else {
        _setError(response.message ?? 'Check-out failed');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Check-out error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<Position?> _getCurrentPosition() async {
    try {
      // Check if location services are enabled
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        _setError('Location services are disabled');
        return null;
      }
      
      // Check location permissions
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          _setError('Location permissions are denied');
          return null;
        }
      }
      
      if (permission == LocationPermission.deniedForever) {
        _setError('Location permissions are permanently denied');
        return null;
      }
      
      // Get current position
      return await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
        timeLimit: const Duration(seconds: 10),
      );
    } catch (e) {
      _setError('Failed to get location: $e');
      return null;
    }
  }
  
  Future<bool> requestLocationPermission() async {
    try {
      final status = await Permission.location.request();
      return status.isGranted;
    } catch (e) {
      _setError('Failed to request location permission: $e');
      return false;
    }
  }
  
  bool isWithinAttendanceArea(Position position) {
    if (_settings == null) return false;
    
    final distance = Geolocator.distanceBetween(
      _settings!.latitude,
      _settings!.longitude,
      position.latitude,
      position.longitude,
    );
    
    return distance <= _settings!.radius;
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
import 'dart:io';
import 'package:flutter/foundation.dart';
import '../models/leave.dart';
import '../services/api_service.dart';

class LeaveProvider with ChangeNotifier {
  final ApiService _apiService;
  
  List<Leave> _leaves = [];
  List<Leave> _pendingApprovals = [];
  bool _isLoading = false;
  String? _error;
  
  LeaveProvider(this._apiService);
  
  List<Leave> get leaves => _leaves;
  List<Leave> get pendingApprovals => _pendingApprovals;
  bool get isLoading => _isLoading;
  String? get error => _error;
  
  Future<void> loadLeaves({
    String? status,
    String? leaveType,
    DateTime? startDate,
    DateTime? endDate,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final queryParams = <String, String>{};
      if (status != null) queryParams['status'] = status;
      if (leaveType != null) queryParams['leave_type'] = leaveType;
      if (startDate != null) {
        queryParams['start_date'] = startDate.toIso8601String().split('T')[0];
      }
      if (endDate != null) {
        queryParams['end_date'] = endDate.toIso8601String().split('T')[0];
      }
      
      final uri = Uri(
        path: '/leaves',
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );
      
      final response = await _apiService.get(uri.toString());
      
      if (response.success && response.data != null) {
        final List<dynamic> leaveData = response.data['data'];
        _leaves = leaveData.map((json) => Leave.fromJson(json)).toList();
        
        // Filter pending approvals (for users who can approve)
        _pendingApprovals = _leaves
            .where((leave) => leave.isPending)
            .toList();
      }
    } catch (e) {
      _setError('Failed to load leaves: $e');
    }
    
    _setLoading(false);
  }
  
  Future<bool> submitLeaveRequest({
    required String leaveType,
    required DateTime startDate,
    required DateTime endDate,
    required String reason,
    File? attachment,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final fields = <String, String>{
        'leave_type': leaveType,
        'start_date': startDate.toIso8601String().split('T')[0],
        'end_date': endDate.toIso8601String().split('T')[0],
        'reason': reason,
      };
      
      List<MultipartFile>? files;
      if (attachment != null) {
        files = [
          MultipartFile(
            field: 'attachment',
            file: attachment,
            filename: 'leave_attachment_${DateTime.now().millisecondsSinceEpoch}.${attachment.path.split('.').last}',
          ),
        ];
      }
      
      final response = await _apiService.postMultipart(
        '/leaves',
        fields,
        files: files,
      );
      
      if (response.success) {
        await loadLeaves(); // Reload leaves
        _setLoading(false);
        return true;
      } else {
        _setError(response.message ?? 'Failed to submit leave request');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Submit leave error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<bool> approveLeave(int leaveId, {String? comment}) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final data = <String, dynamic>{};
      if (comment != null && comment.isNotEmpty) {
        data['comment'] = comment;
      }
      
      final response = await _apiService.post(
        '/leaves/$leaveId/approve',
        data: data,
      );
      
      if (response.success) {
        await loadLeaves(); // Reload leaves
        _setLoading(false);
        return true;
      } else {
        _setError(response.message ?? 'Failed to approve leave');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Approve leave error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<bool> rejectLeave(int leaveId, {required String comment}) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final response = await _apiService.post(
        '/leaves/$leaveId/reject',
        data: {'comment': comment},
      );
      
      if (response.success) {
        await loadLeaves(); // Reload leaves
        _setLoading(false);
        return true;
      } else {
        _setError(response.message ?? 'Failed to reject leave');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Reject leave error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<Leave?> getLeaveDetails(int leaveId) async {
    try {
      final response = await _apiService.get('/leaves/$leaveId');
      
      if (response.success && response.data != null) {
        return Leave.fromJson(response.data);
      }
    } catch (e) {
      _setError('Failed to get leave details: $e');
    }
    
    return null;
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
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  final ApiService _apiService;
  final SharedPreferences _prefs;
  
  User? _user;
  bool _isLoading = false;
  String? _error;
  
  AuthProvider(this._apiService, this._prefs) {
    _loadUserFromStorage();
  }
  
  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isAuthenticated => _user != null && _apiService.token != null;
  
  Future<void> _loadUserFromStorage() async {
    final userJson = _prefs.getString('user_data');
    if (userJson != null && _apiService.token != null) {
      try {
        _user = User.fromJson(userJson as Map<String, dynamic>);
        notifyListeners();
      } catch (e) {
        // Clear invalid data
        await _clearAuthData();
      }
    }
  }
  
  Future<bool> login(String email, String password) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final response = await _apiService.post('/login', data: {
        'email': email,
        'password': password,
      });
      
      if (response.success && response.data != null) {
        final userData = response.data['user'];
        final token = response.data['token'];
        
        _user = User.fromJson(userData);
        await _apiService.saveToken(token);
        await _prefs.setString('user_data', userData.toString());
        
        _setLoading(false);
        notifyListeners();
        return true;
      } else {
        _setError(response.message ?? 'Login failed');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Network error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<void> logout() async {
    _setLoading(true);
    
    try {
      await _apiService.post('/logout');
    } catch (e) {
      // Continue with logout even if API call fails
      debugPrint('Logout API error: $e');
    }
    
    await _clearAuthData();
    _setLoading(false);
    notifyListeners();
  }
  
  Future<bool> updateProfile({
    required String name,
    String? currentPassword,
    String? newPassword,
    String? confirmPassword,
  }) async {
    _setLoading(true);
    _setError(null);
    
    try {
      final data = <String, dynamic>{
        'name': name,
      };
      
      if (currentPassword != null && currentPassword.isNotEmpty) {
        data['current_password'] = currentPassword;
      }
      
      if (newPassword != null && newPassword.isNotEmpty) {
        data['password'] = newPassword;
        data['password_confirmation'] = confirmPassword;
      }
      
      final response = await _apiService.put('/profile', data: data);
      
      if (response.success && response.data != null) {
        _user = User.fromJson(response.data);
        await _prefs.setString('user_data', response.data.toString());
        
        _setLoading(false);
        notifyListeners();
        return true;
      } else {
        _setError(response.message ?? 'Update failed');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('Network error: $e');
      _setLoading(false);
      return false;
    }
  }
  
  Future<void> refreshProfile() async {
    if (!isAuthenticated) return;
    
    try {
      final response = await _apiService.get('/profile');
      
      if (response.success && response.data != null) {
        _user = User.fromJson(response.data);
        await _prefs.setString('user_data', response.data.toString());
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Refresh profile error: $e');
    }
  }
  
  Future<void> _clearAuthData() async {
    _user = null;
    await _apiService.removeToken();
    await _prefs.remove('user_data');
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
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:logger/logger.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api'; // Change this to your Laravel backend URL
  
  final SharedPreferences prefs;
  final Logger logger = Logger();
  
  ApiService(this.prefs);
  
  String? get token => prefs.getString('auth_token');
  
  Map<String, String> get headers {
    final Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    
    return headers;
  }
  
  Future<void> saveToken(String token) async {
    await prefs.setString('auth_token', token);
  }
  
  Future<void> removeToken() async {
    await prefs.remove('auth_token');
  }
  
  Future<ApiResponse> get(String endpoint) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
      );
      
      return _handleResponse(response);
    } catch (e) {
      logger.e('GET $endpoint error: $e');
      return ApiResponse(
        success: false,
        message: 'Network error: $e',
      );
    }
  }
  
  Future<ApiResponse> post(String endpoint, {Map<String, dynamic>? data}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
        body: data != null ? jsonEncode(data) : null,
      );
      
      return _handleResponse(response);
    } catch (e) {
      logger.e('POST $endpoint error: $e');
      return ApiResponse(
        success: false,
        message: 'Network error: $e',
      );
    }
  }
  
  Future<ApiResponse> put(String endpoint, {Map<String, dynamic>? data}) async {
    try {
      final response = await http.put(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
        body: data != null ? jsonEncode(data) : null,
      );
      
      return _handleResponse(response);
    } catch (e) {
      logger.e('PUT $endpoint error: $e');
      return ApiResponse(
        success: false,
        message: 'Network error: $e',
      );
    }
  }
  
  Future<ApiResponse> delete(String endpoint) async {
    try {
      final response = await http.delete(
        Uri.parse('$baseUrl$endpoint'),
        headers: headers,
      );
      
      return _handleResponse(response);
    } catch (e) {
      logger.e('DELETE $endpoint error: $e');
      return ApiResponse(
        success: false,
        message: 'Network error: $e',
      );
    }
  }
  
  Future<ApiResponse> postMultipart(
    String endpoint,
    Map<String, String> fields, {
    List<MultipartFile>? files,
  }) async {
    try {
      final uri = Uri.parse('$baseUrl$endpoint');
      final request = http.MultipartRequest('POST', uri);
      
      // Add headers
      if (token != null) {
        request.headers['Authorization'] = 'Bearer $token';
      }
      request.headers['Accept'] = 'application/json';
      
      // Add fields
      request.fields.addAll(fields);
      
      // Add files
      if (files != null) {
        request.files.addAll(files);
      }
      
      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      
      return _handleResponse(response);
    } catch (e) {
      logger.e('POST MULTIPART $endpoint error: $e');
      return ApiResponse(
        success: false,
        message: 'Network error: $e',
      );
    }
  }
  
  ApiResponse _handleResponse(http.Response response) {
    logger.d('Response status: ${response.statusCode}');
    logger.d('Response body: ${response.body}');
    
    try {
      final Map<String, dynamic> responseData = jsonDecode(response.body);
      
      if (response.statusCode >= 200 && response.statusCode < 300) {
        return ApiResponse(
          success: responseData['success'] ?? true,
          message: responseData['message'],
          data: responseData['data'],
        );
      } else {
        return ApiResponse(
          success: false,
          message: responseData['message'] ?? 'Unknown error occurred',
          errors: responseData['errors'],
        );
      }
    } catch (e) {
      logger.e('JSON decode error: $e');
      return ApiResponse(
        success: false,
        message: 'Invalid response format',
      );
    }
  }
}

class ApiResponse {
  final bool success;
  final String? message;
  final dynamic data;
  final Map<String, dynamic>? errors;
  
  ApiResponse({
    required this.success,
    this.message,
    this.data,
    this.errors,
  });
  
  @override
  String toString() {
    return 'ApiResponse(success: $success, message: $message, data: $data, errors: $errors)';
  }
}

class MultipartFile {
  final String field;
  final File file;
  final String? filename;
  final String? contentType;
  
  MultipartFile({
    required this.field,
    required this.file,
    this.filename,
    this.contentType,
  });
  
  http.MultipartFile toHttpMultipartFile() {
    return http.MultipartFile.fromBytes(
      field,
      file.readAsBytesSync(),
      filename: filename ?? file.path.split('/').last,
    );
  }
}
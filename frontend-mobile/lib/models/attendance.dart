class Attendance {
  final int id;
  final int userId;
  final String type; // checkin, checkout
  final double? latitude;
  final double? longitude;
  final double? accuracy;
  final String? photoPath;
  final int? qrTokenId;
  final String status; // hadir, terlambat, izin, sakit, alpha, cuti, dinas_luar
  final DateTime timestamp;
  final bool synced;
  final User? user;
  
  Attendance({
    required this.id,
    required this.userId,
    required this.type,
    this.latitude,
    this.longitude,
    this.accuracy,
    this.photoPath,
    this.qrTokenId,
    required this.status,
    required this.timestamp,
    required this.synced,
    this.user,
  });
  
  factory Attendance.fromJson(Map<String, dynamic> json) {
    return Attendance(
      id: json['id'],
      userId: json['user_id'],
      type: json['type'],
      latitude: json['latitude']?.toDouble(),
      longitude: json['longitude']?.toDouble(),
      accuracy: json['accuracy']?.toDouble(),
      photoPath: json['photo_path'],
      qrTokenId: json['qr_token_id'],
      status: json['status'],
      timestamp: DateTime.parse(json['timestamp']),
      synced: json['synced'] ?? false,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'type': type,
      'latitude': latitude,
      'longitude': longitude,
      'accuracy': accuracy,
      'photo_path': photoPath,
      'qr_token_id': qrTokenId,
      'status': status,
      'timestamp': timestamp.toIso8601String(),
      'synced': synced,
      'user': user?.toJson(),
    };
  }
  
  bool get isCheckIn => type == 'checkin';
  bool get isCheckOut => type == 'checkout';
  
  String get statusLabel {
    switch (status) {
      case 'hadir':
        return 'Hadir';
      case 'terlambat':
        return 'Terlambat';
      case 'izin':
        return 'Izin';
      case 'sakit':
        return 'Sakit';
      case 'alpha':
        return 'Alpha';
      case 'cuti':
        return 'Cuti';
      case 'dinas_luar':
        return 'Dinas Luar';
      default:
        return status;
    }
  }
  
  String get typeLabel {
    switch (type) {
      case 'checkin':
        return 'Check In';
      case 'checkout':
        return 'Check Out';
      default:
        return type;
    }
  }
  
  @override
  String toString() {
    return 'Attendance(id: $id, type: $type, status: $status, timestamp: $timestamp)';
  }
}
class Leave {
  final int id;
  final int userId;
  final String leaveType; // izin, sakit, cuti, dinas_luar
  final DateTime startDate;
  final DateTime endDate;
  final String? reason;
  final String? attachmentPath;
  final String status; // menunggu, disetujui, ditolak
  final int? approvedBy;
  final DateTime? approvedAt;
  final DateTime createdAt;
  final User? user;
  final User? approver;
  
  Leave({
    required this.id,
    required this.userId,
    required this.leaveType,
    required this.startDate,
    required this.endDate,
    this.reason,
    this.attachmentPath,
    required this.status,
    this.approvedBy,
    this.approvedAt,
    required this.createdAt,
    this.user,
    this.approver,
  });
  
  factory Leave.fromJson(Map<String, dynamic> json) {
    return Leave(
      id: json['id'],
      userId: json['user_id'],
      leaveType: json['leave_type'],
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      reason: json['reason'],
      attachmentPath: json['attachment_path'],
      status: json['status'],
      approvedBy: json['approved_by'],
      approvedAt: json['approved_at'] != null 
          ? DateTime.parse(json['approved_at'])
          : null,
      createdAt: DateTime.parse(json['created_at']),
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      approver: json['approver'] != null ? User.fromJson(json['approver']) : null,
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'leave_type': leaveType,
      'start_date': startDate.toIso8601String().split('T')[0],
      'end_date': endDate.toIso8601String().split('T')[0],
      'reason': reason,
      'attachment_path': attachmentPath,
      'status': status,
      'approved_by': approvedBy,
      'approved_at': approvedAt?.toIso8601String(),
      'created_at': createdAt.toIso8601String(),
      'user': user?.toJson(),
      'approver': approver?.toJson(),
    };
  }
  
  int get durationDays {
    return endDate.difference(startDate).inDays + 1;
  }
  
  bool get isPending => status == 'menunggu';
  bool get isApproved => status == 'disetujui';
  bool get isRejected => status == 'ditolak';
  
  String get leaveTypeLabel {
    switch (leaveType) {
      case 'izin':
        return 'Izin';
      case 'sakit':
        return 'Sakit';
      case 'cuti':
        return 'Cuti';
      case 'dinas_luar':
        return 'Dinas Luar';
      default:
        return leaveType;
    }
  }
  
  String get statusLabel {
    switch (status) {
      case 'menunggu':
        return 'Menunggu Persetujuan';
      case 'disetujui':
        return 'Disetujui';
      case 'ditolak':
        return 'Ditolak';
      default:
        return status;
    }
  }
  
  @override
  String toString() {
    return 'Leave(id: $id, leaveType: $leaveType, status: $status, startDate: $startDate, endDate: $endDate)';
  }
}
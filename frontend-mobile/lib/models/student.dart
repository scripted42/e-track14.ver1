class Student {
  final int id;
  final String name;
  final String className;
  final String cardQrCode;
  final DateTime createdAt;
  
  Student({
    required this.id,
    required this.name,
    required this.className,
    required this.cardQrCode,
    required this.createdAt,
  });
  
  factory Student.fromJson(Map<String, dynamic> json) {
    return Student(
      id: json['id'],
      name: json['name'],
      className: json['class_name'],
      cardQrCode: json['card_qr_code'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'class_name': className,
      'card_qr_code': cardQrCode,
      'created_at': createdAt.toIso8601String(),
    };
  }
  
  @override
  String toString() {
    return 'Student(id: $id, name: $name, className: $className)';
  }
}

class StudentAttendance {
  final int id;
  final int studentId;
  final int teacherId;
  final String status; // hadir, izin, sakit, alpha
  final DateTime createdAt;
  final Student? student;
  final User? teacher;
  
  StudentAttendance({
    required this.id,
    required this.studentId,
    required this.teacherId,
    required this.status,
    required this.createdAt,
    this.student,
    this.teacher,
  });
  
  factory StudentAttendance.fromJson(Map<String, dynamic> json) {
    return StudentAttendance(
      id: json['id'],
      studentId: json['student_id'],
      teacherId: json['teacher_id'],
      status: json['status'],
      createdAt: DateTime.parse(json['created_at']),
      student: json['student'] != null ? Student.fromJson(json['student']) : null,
      teacher: json['teacher'] != null ? User.fromJson(json['teacher']) : null,
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'student_id': studentId,
      'teacher_id': teacherId,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'student': student?.toJson(),
      'teacher': teacher?.toJson(),
    };
  }
  
  String get statusLabel {
    switch (status) {
      case 'hadir':
        return 'Hadir';
      case 'izin':
        return 'Izin';
      case 'sakit':
        return 'Sakit';
      case 'alpha':
        return 'Alpha';
      default:
        return status;
    }
  }
  
  @override
  String toString() {
    return 'StudentAttendance(id: $id, status: $status, createdAt: $createdAt)';
  }
}
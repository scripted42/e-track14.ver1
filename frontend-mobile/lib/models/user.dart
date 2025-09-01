class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final DateTime? createdAt;
  
  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.createdAt,
  });
  
  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at'])
          : null,
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'created_at': createdAt?.toIso8601String(),
    };
  }
  
  bool get isAdmin => role == 'Admin';
  bool get isTeacher => role == 'Guru';
  bool get isEmployee => role == 'Pegawai';
  bool get isStudent => role == 'Siswa';
  bool get isVicePrincipal => role == 'Waka Kurikulum';
  
  bool get canApproveLeaves => isAdmin || isVicePrincipal;
  bool get canMarkStudentAttendance => isTeacher || isAdmin;
  
  @override
  String toString() {
    return 'User(id: $id, name: $name, email: $email, role: $role)';
  }
}
class AppSettings {
  final double latitude;
  final double longitude;
  final int radius;
  final String checkinStart;
  final String checkinEnd;
  final String checkoutStart;
  final String checkoutEnd;
  
  AppSettings({
    required this.latitude,
    required this.longitude,
    required this.radius,
    required this.checkinStart,
    required this.checkinEnd,
    required this.checkoutStart,
    required this.checkoutEnd,
  });
  
  factory AppSettings.fromJson(Map<String, dynamic> json) {
    return AppSettings(
      latitude: json['latitude'].toDouble(),
      longitude: json['longitude'].toDouble(),
      radius: json['radius'],
      checkinStart: json['checkin_start'],
      checkinEnd: json['checkin_end'],
      checkoutStart: json['checkout_start'],
      checkoutEnd: json['checkout_end'],
    );
  }
  
  Map<String, dynamic> toJson() {
    return {
      'latitude': latitude,
      'longitude': longitude,
      'radius': radius,
      'checkin_start': checkinStart,
      'checkin_end': checkinEnd,
      'checkout_start': checkoutStart,
      'checkout_end': checkoutEnd,
    };
  }
  
  @override
  String toString() {
    return 'AppSettings(lat: $latitude, lng: $longitude, radius: $radius)';
  }
}
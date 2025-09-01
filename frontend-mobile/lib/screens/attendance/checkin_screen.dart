import 'package:flutter/material.dart';

class CheckinScreen extends StatelessWidget {
  const CheckinScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Check In'),
      ),
      body: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.login,
              size: 64,
              color: Colors.green,
            ),
            SizedBox(height: 16),
            Text(
              'Check In Screen',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
            ),
            SizedBox(height: 8),
            Text(
              'Camera + GPS + QR Scanner Implementation',
              style: TextStyle(color: Colors.grey),
            ),
          ],
        ),
      ),
    );
  }
}
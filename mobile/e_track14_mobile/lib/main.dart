import 'package:flutter/material.dart';

void main() {
  runApp(const ETrack14App());
}

class ETrack14App extends StatelessWidget {
  const ETrack14App({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'E-Track14',
      theme: ThemeData(
        useMaterial3: true,
        colorSchemeSeed: Colors.blue,
      ),
      home: const Scaffold(
        body: Center(child: Text('E-Track14 Draft')), 
      ),
    );
  }
}



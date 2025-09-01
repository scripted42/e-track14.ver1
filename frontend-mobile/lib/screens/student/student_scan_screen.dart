import 'dart:io';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:qr_code_scanner/qr_code_scanner.dart';
import 'package:permission_handler/permission_handler.dart';
import '../../providers/student_attendance_provider.dart';
import '../../models/student.dart';

class StudentScanScreen extends StatefulWidget {
  const StudentScanScreen({super.key});

  @override
  State<StudentScanScreen> createState() => _StudentScanScreenState();
}

class _StudentScanScreenState extends State<StudentScanScreen> {
  final GlobalKey qrKey = GlobalKey(debugLabel: 'QR');
  QRViewController? controller;
  bool isScanning = true;
  bool hasPermission = false;

  @override
  void initState() {
    super.initState();
    _requestCameraPermission();
  }

  @override
  void reassemble() {
    super.reassemble();
    if (Platform.isAndroid) {
      controller!.pauseCamera();
    } else if (Platform.isIOS) {
      controller!.resumeCamera();
    }
  }

  @override
  void dispose() {
    controller?.dispose();
    super.dispose();
  }

  Future<void> _requestCameraPermission() async {
    final status = await Permission.camera.request();
    setState(() {
      hasPermission = status.isGranted;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Scan QR Siswa'),
        actions: [
          IconButton(
            onPressed: () => _toggleFlash(),
            icon: const Icon(Icons.flash_on),
          ),
          IconButton(
            onPressed: () => _toggleCamera(),
            icon: const Icon(Icons.flip_camera_android),
          ),
        ],
      ),
      body: !hasPermission
          ? _buildPermissionRequest()
          : Column(
              children: [
                Expanded(flex: 4, child: _buildQrView()),
                Expanded(flex: 1, child: _buildScanInstructions()),
              ],
            ),
    );
  }

  Widget _buildPermissionRequest() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.camera_alt_outlined,
            size: 64,
            color: Colors.grey,
          ),
          const SizedBox(height: 16),
          const Text(
            'Izin Kamera Diperlukan',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Aplikasi memerlukan akses kamera untuk memindai QR code kartu siswa',
            textAlign: TextAlign.center,
            style: TextStyle(color: Colors.grey),
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: _requestCameraPermission,
            child: const Text('Berikan Izin Kamera'),
          ),
        ],
      ),
    );
  }

  Widget _buildQrView() {
    return QRView(
      key: qrKey,
      onQRViewCreated: _onQRViewCreated,
      overlay: QrScannerOverlayShape(
        borderColor: Theme.of(context).colorScheme.primary,
        borderRadius: 10,
        borderLength: 30,
        borderWidth: 10,
        cutOutSize: 300,
      ),
    );
  }

  Widget _buildScanInstructions() {
    return Container(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          Icon(
            Icons.qr_code_scanner,
            size: 48,
            color: Theme.of(context).colorScheme.primary,
          ),
          const SizedBox(height: 8),
          const Text(
            'Arahkan kamera ke QR code pada kartu siswa',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w500,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 4),
          Text(
            'Pastikan QR code berada di dalam frame',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey[600],
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  void _onQRViewCreated(QRViewController controller) {
    this.controller = controller;
    controller.scannedDataStream.listen((scanData) {
      if (isScanning && scanData.code != null) {
        _handleQRScan(scanData.code!);
      }
    });
  }

  Future<void> _handleQRScan(String qrCode) async {
    if (!isScanning) return;

    setState(() {
      isScanning = false;
    });

    // Pause camera to stop scanning
    await controller?.pauseCamera();

    try {
      final provider = context.read<StudentAttendanceProvider>();
      final result = await provider.scanStudent(qrCode);

      if (mounted) {
        if (result?.success == true) {
          _showSuccessDialog(result!.student!, result.attendance!);
        } else {
          _showErrorDialog(result?.message ?? 'QR code tidak valid');
        }
      }
    } catch (e) {
      if (mounted) {
        _showErrorDialog('Error: $e');
      }
    }
  }

  void _showSuccessDialog(Student student, StudentAttendance attendance) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        icon: const Icon(
          Icons.check_circle,
          color: Colors.green,
          size: 48,
        ),
        title: const Text('Absensi Berhasil'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Siswa: ${student.name}',
              style: const TextStyle(fontWeight: FontWeight.w600),
            ),
            Text('Kelas: ${student.className}'),
            Text('Status: ${attendance.statusLabel}'),
            Text(
              'Waktu: ${DateTime.now().toString().substring(11, 16)}',
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              _resumeScanning();
            },
            child: const Text('Scan Lagi'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.of(context).pop();
              Navigator.of(context).pop(); // Go back to previous screen
            },
            child: const Text('Selesai'),
          ),
        ],
      ),
    );
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        icon: const Icon(
          Icons.error,
          color: Colors.red,
          size: 48,
        ),
        title: const Text('Scan Gagal'),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              _resumeScanning();
            },
            child: const Text('Coba Lagi'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.of(context).pop();
              Navigator.of(context).pop(); // Go back to previous screen
            },
            child: const Text('Kembali'),
          ),
        ],
      ),
    );
  }

  void _resumeScanning() {
    setState(() {
      isScanning = true;
    });
    controller?.resumeCamera();
  }

  Future<void> _toggleFlash() async {
    await controller?.toggleFlash();
  }

  Future<void> _toggleCamera() async {
    await controller?.flipCamera();
  }
}
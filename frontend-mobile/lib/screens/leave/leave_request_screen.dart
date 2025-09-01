import 'dart:io';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import 'package:intl/intl.dart';
import '../../providers/leave_provider.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/loading_button.dart';

class LeaveRequestScreen extends StatefulWidget {
  const LeaveRequestScreen({super.key});

  @override
  State<LeaveRequestScreen> createState() => _LeaveRequestScreenState();
}

class _LeaveRequestScreenState extends State<LeaveRequestScreen> {
  final _formKey = GlobalKey<FormState>();
  final _reasonController = TextEditingController();

  String? selectedLeaveType;
  DateTime? startDate;
  DateTime? endDate;
  File? attachmentFile;
  String? attachmentFileName;

  final List<Map<String, String>> leaveTypes = [
    {'value': 'izin', 'label': 'Izin'},
    {'value': 'sakit', 'label': 'Sakit'},
    {'value': 'cuti', 'label': 'Cuti'},
    {'value': 'dinas_luar', 'label': 'Dinas Luar'},
  ];

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Ajukan Izin'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildLeaveTypeSelection(),
              const SizedBox(height: 16),
              _buildDateSelection(),
              const SizedBox(height: 16),
              _buildReasonField(),
              const SizedBox(height: 16),
              _buildAttachmentSection(),
              const SizedBox(height: 32),
              _buildSubmitButton(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLeaveTypeSelection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Jenis Izin',
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: selectedLeaveType,
          decoration: const InputDecoration(
            hintText: 'Pilih jenis izin',
            border: OutlineInputBorder(),
          ),
          items: leaveTypes.map((type) {
            return DropdownMenuItem<String>(
              value: type['value'],
              child: Row(
                children: [
                  Icon(
                    _getLeaveTypeIcon(type['value']!),
                    size: 20,
                    color: _getLeaveTypeColor(type['value']!),
                  ),
                  const SizedBox(width: 8),
                  Text(type['label']!),
                ],
              ),
            );
          }).toList(),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Jenis izin harus dipilih';
            }
            return null;
          },
          onChanged: (value) {
            setState(() {
              selectedLeaveType = value;
            });
          },
        ),
      ],
    );
  }

  Widget _buildDateSelection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Periode Izin',
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 8),
        Row(
          children: [
            Expanded(
              child: _buildDateField(
                'Tanggal Mulai',
                startDate,
                (date) => setState(() => startDate = date),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildDateField(
                'Tanggal Selesai',
                endDate,
                (date) => setState(() => endDate = date),
                minDate: startDate,
              ),
            ),
          ],
        ),
        if (startDate != null && endDate != null)
          Padding(
            padding: const EdgeInsets.only(top: 8),
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.blue[200]!),
              ),
              child: Row(
                children: [
                  Icon(
                    Icons.info,
                    size: 16,
                    color: Colors.blue[700],
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Durasi: ${_calculateDuration()} hari',
                    style: TextStyle(
                      color: Colors.blue[700],
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildDateField(
    String label,
    DateTime? selectedDate,
    Function(DateTime) onDateSelected, {
    DateTime? minDate,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 4),
        InkWell(
          onTap: () => _selectDate(onDateSelected, minDate),
          child: Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 16),
            decoration: BoxDecoration(
              border: Border.all(color: Colors.grey[300]!),
              borderRadius: BorderRadius.circular(4),
              color: Colors.grey[50],
            ),
            child: Row(
              children: [
                Icon(
                  Icons.calendar_today,
                  size: 16,
                  color: Colors.grey[600],
                ),
                const SizedBox(width: 8),
                Text(
                  selectedDate != null
                      ? DateFormat('dd/MM/yyyy').format(selectedDate)
                      : 'Pilih tanggal',
                  style: TextStyle(
                    color: selectedDate != null ? Colors.black : Colors.grey[600],
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildReasonField() {
    return CustomTextField(
      controller: _reasonController,
      label: 'Alasan Izin',
      hintText: 'Jelaskan alasan pengajuan izin...',
      maxLines: 4,
      validator: (value) {
        if (value == null || value.trim().isEmpty) {
          return 'Alasan izin harus diisi';
        }
        if (value.trim().length < 10) {
          return 'Alasan terlalu singkat (minimal 10 karakter)';
        }
        return null;
      },
    );
  }

  Widget _buildAttachmentSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Lampiran (Opsional)',
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 8),
        if (attachmentFile == null)
          OutlinedButton.icon(
            onPressed: _pickFile,
            icon: const Icon(Icons.attach_file),
            label: const Text('Pilih File'),
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.all(16),
            ),
          )
        else
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.green[50],
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: Colors.green[200]!),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.description,
                  color: Colors.green[700],
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    attachmentFileName ?? 'File terpilih',
                    style: TextStyle(
                      color: Colors.green[700],
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
                IconButton(
                  onPressed: () {
                    setState(() {
                      attachmentFile = null;
                      attachmentFileName = null;
                    });
                  },
                  icon: Icon(
                    Icons.close,
                    color: Colors.green[700],
                  ),
                ),
              ],
            ),
          ),
        const SizedBox(height: 8),
        Text(
          'Format yang didukung: PDF, JPG, PNG (Maks. 2MB)',
          style: TextStyle(
            fontSize: 12,
            color: Colors.grey[600],
          ),
        ),
      ],
    );
  }

  Widget _buildSubmitButton() {
    return Consumer<LeaveProvider>(
      builder: (context, provider, child) {
        return LoadingButton(
          onPressed: _submitLeaveRequest,
          isLoading: provider.isLoading,
          child: const Text('Ajukan Izin'),
        );
      },
    );
  }

  IconData _getLeaveTypeIcon(String type) {
    switch (type) {
      case 'izin':
        return Icons.schedule;
      case 'sakit':
        return Icons.local_hospital;
      case 'cuti':
        return Icons.beach_access;
      case 'dinas_luar':
        return Icons.business_center;
      default:
        return Icons.event_busy;
    }
  }

  Color _getLeaveTypeColor(String type) {
    switch (type) {
      case 'izin':
        return Colors.blue;
      case 'sakit':
        return Colors.red;
      case 'cuti':
        return Colors.green;
      case 'dinas_luar':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }

  int _calculateDuration() {
    if (startDate == null || endDate == null) return 0;
    return endDate!.difference(startDate!).inDays + 1;
  }

  Future<void> _selectDate(
    Function(DateTime) onDateSelected,
    DateTime? minDate,
  ) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: minDate ?? DateTime.now(),
      firstDate: minDate ?? DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (picked != null) {
      onDateSelected(picked);
    }
  }

  Future<void> _pickFile() async {
    try {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png'],
        allowMultiple: false,
      );

      if (result != null && result.files.single.path != null) {
        final file = File(result.files.single.path!);
        final fileSize = await file.length();

        // Check file size (2MB limit)
        if (fileSize > 2 * 1024 * 1024) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('Ukuran file terlalu besar (maksimal 2MB)'),
                backgroundColor: Colors.red,
              ),
            );
          }
          return;
        }

        setState(() {
          attachmentFile = file;
          attachmentFileName = result.files.single.name;
        });
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error memilih file: $e'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Future<void> _submitLeaveRequest() async {
    if (!_formKey.currentState!.validate()) return;

    if (startDate == null || endDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tanggal mulai dan selesai harus dipilih'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (endDate!.isBefore(startDate!)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tanggal selesai tidak boleh sebelum tanggal mulai'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final provider = context.read<LeaveProvider>();
    final success = await provider.submitLeaveRequest(
      leaveType: selectedLeaveType!,
      startDate: startDate!,
      endDate: endDate!,
      reason: _reasonController.text.trim(),
      attachment: attachmentFile,
    );

    if (mounted) {
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Pengajuan izin berhasil dikirim'),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.of(context).pop();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              provider.error ?? 'Gagal mengajukan izin',
            ),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }
}
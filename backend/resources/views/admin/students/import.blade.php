@extends('admin.layouts.app')

@section('title', 'Import Siswa')

@push('styles')
<style>
.import-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    margin-bottom: 1.5rem;
}

.import-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px 8px 0 0;
}

.import-body {
    padding: 1.5rem;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: #28a745;
    color: white;
}

.step.completed .step-number {
    background: #20c997;
    color: white;
}

.step-label {
    font-size: 0.875rem;
    color: #6c757d;
    text-align: center;
}

.step.active .step-label {
    color: #28a745;
    font-weight: 600;
}

.step.completed .step-label {
    color: #20c997;
    font-weight: 600;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 3rem 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #28a745;
    background: #f0fff4;
}

.upload-area.dragover {
    border-color: #28a745;
    background: #e8f5e8;
}

.upload-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.preview-table {
    max-height: 400px;
    overflow-y: auto;
}

.table th {
    background: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
    position: sticky;
    top: 0;
    z-index: 10;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.import-summary {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #dee2e6;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-label {
    font-weight: 600;
    color: #495057;
}

.summary-value {
    font-weight: bold;
}

.btn-import {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-import:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    color: white;
}

.btn-template {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-template:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    color: white;
}

.loading-spinner {
    display: none;
}

.loading-spinner.show {
    display: inline-block;
}

.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
}
</style>

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Import Siswa</h1>
            <p class="text-muted mb-0">Import data siswa secara massal dari file Excel</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" id="step1">
            <div class="step-number">1</div>
            <div class="step-label">Upload File</div>
        </div>
        <div class="step" id="step2">
            <div class="step-number">2</div>
            <div class="step-label">Preview Data</div>
        </div>
        <div class="step" id="step3">
            <div class="step-number">3</div>
            <div class="step-label">Import Data</div>
        </div>
    </div>

    <!-- Step 1: Upload File -->
    <div class="import-card" id="uploadStep">
        <div class="import-header">
            <h5 class="mb-0">
                <i class="fas fa-upload me-2"></i>Upload File Excel
            </h5>
        </div>
        <div class="import-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi:</strong> Download template Excel terlebih dahulu untuk memastikan format data yang benar.
            </div>
            
            <div class="text-center mb-3">
                <a href="{{ route('admin.students.import.template') }}" class="btn btn-template">
                    <i class="fas fa-download me-2"></i>Download Template Excel
                </a>
            </div>

            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h5>Drag & Drop file Excel di sini</h5>
                    <p class="text-muted mb-3">atau klik untuk memilih file</p>
                    <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" style="display: none;">
                    <button type="button" class="btn btn-outline-success" id="selectFileBtn">
                        <i class="fas fa-folder-open me-2"></i>Pilih File
                    </button>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success" id="previewBtn" disabled>
                        <i class="fas fa-eye me-2"></i>Preview Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Step 2: Preview Data -->
    <div class="import-card" id="previewStep" style="display: none;">
        <div class="import-header">
            <h5 class="mb-0">
                <i class="fas fa-eye me-2"></i>Preview Data
            </h5>
        </div>
        <div class="import-body">
            <div class="import-summary" id="importSummary">
                <!-- Summary will be populated by JavaScript -->
            </div>
            
            <div class="preview-table">
                <table class="table table-bordered table-striped" id="previewTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th>QR Code</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="previewTableBody">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <button type="button" class="btn btn-outline-secondary me-2" onclick="backToUpload()">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </button>
                <button type="button" class="btn btn-import" id="importBtn">
                    <i class="fas fa-upload me-2"></i>Import Data
                </button>
            </div>
        </div>
    </div>

    <!-- Step 3: Import Progress -->
    <div class="import-card" id="importStep" style="display: none;">
        <div class="import-header">
            <h5 class="mb-0">
                <i class="fas fa-cog fa-spin me-2"></i>Sedang Import Data...
            </h5>
        </div>
        <div class="import-body text-center">
            <div class="loading-spinner show">
                <i class="fas fa-spinner fa-spin fa-3x text-success mb-3"></i>
            </div>
            <h5>Mohon tunggu, data sedang diproses...</h5>
            <p class="text-muted">Proses ini mungkin memakan waktu beberapa menit tergantung jumlah data.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
let previewData = [];
let validationResults = null;
let fileData = null;

// Function to attach upload listeners
function attachUploadListeners() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const previewBtn = document.getElementById('previewBtn');
    
    if (!uploadArea) {
        console.error('Upload area not found');
        return;
    }
    
    if (!fileInput) {
        console.error('File input not found');
        return;
    }
    
    if (!previewBtn) {
        console.error('Preview button not found');
        return;
    }

// File upload handling
    fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
            // Validate file type
            if (file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || 
                file.type === 'application/vnd.ms-excel' ||
                file.name.endsWith('.xlsx') || file.name.endsWith('.xls')) {
        fileData = file;
                previewBtn.disabled = false;
        updateUploadArea(file.name);
            } else {
                alert('Silakan pilih file Excel (.xlsx atau .xls)');
                this.value = ''; // Clear the input
            }
    }
});

// Drag and drop handling
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || 
                file.type === 'application/vnd.ms-excel' ||
                file.name.endsWith('.xlsx') || file.name.endsWith('.xls')) {
            fileData = file;
                fileInput.files = files;
                previewBtn.disabled = false;
            updateUploadArea(file.name);
        } else {
            alert('Silakan pilih file Excel (.xlsx atau .xls)');
        }
    }
});
    
    // Initial select file button
    const selectFileBtn = document.getElementById('selectFileBtn');
    if (selectFileBtn) {
        selectFileBtn.addEventListener('click', function() {
            fileInput.click();
        });
    }
}

// Initialize upload listeners
attachUploadListeners();

function updateUploadArea(fileName) {
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        // Get the file input element before replacing innerHTML
        const fileInput = document.getElementById('fileInput');
        
    uploadArea.innerHTML = `
        <div class="upload-icon">
            <i class="fas fa-file-excel text-success"></i>
        </div>
        <h5>File terpilih: ${fileName}</h5>
        <p class="text-muted mb-3">Klik "Preview Data" untuk melanjutkan</p>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary" id="changeFileBtn">
            <i class="fas fa-folder-open me-2"></i>Ganti File
        </button>
                <button type="button" class="btn btn-outline-danger" id="removeFileBtn">
                    <i class="fas fa-trash me-2"></i>Hapus File
                </button>
            </div>
        `;
        
        // Re-add the file input to the upload area
        if (fileInput) {
            uploadArea.appendChild(fileInput);
        } else {
            console.error('File input not found to re-add');
        }
        
        // Attach event listeners to new buttons
        const changeFileBtn = document.getElementById('changeFileBtn');
        const removeFileBtn = document.getElementById('removeFileBtn');
        
        if (changeFileBtn) {
            changeFileBtn.addEventListener('click', changeFile);
        } else {
            console.error('Change file button not found');
        }
        
        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', removeFile);
        } else {
            console.error('Remove file button not found');
        }
    }
}

function changeFile() {
    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.click();
    } else {
        console.error('File input not found');
    }
}

function removeFile() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const previewBtn = document.getElementById('previewBtn');
    
    if (uploadArea) {
        uploadArea.innerHTML = `
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt text-muted"></i>
            </div>
            <h5>Drag & Drop file Excel di sini</h5>
            <p class="text-muted mb-3">atau klik untuk memilih file</p>
            <button type="button" class="btn btn-outline-success" id="selectFileBtn">
                <i class="fas fa-folder-open me-2"></i>Pilih File
            </button>
        `;
        
        // Re-add the file input to the upload area
        if (fileInput) {
            uploadArea.appendChild(fileInput);
        } else {
            console.error('File input not found to re-add');
        }
        
        // Re-attach event listeners
        attachUploadListeners();
    }
    
    if (previewBtn) {
        previewBtn.disabled = true;
    }
    
    fileData = null;
    previewData = [];
    validationResults = null;
}

// Form submission for preview
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!fileData) {
        alert('Silakan pilih file terlebih dahulu');
        return;
    }
    
    previewData();
});

function previewData() {
    const formData = new FormData();
    formData.append('file', fileData);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    } else {
        console.error('CSRF token not found');
        return;
    }
    
    // Show loading
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) {
        previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
        previewBtn.disabled = true;
    } else {
        console.error('Preview button not found');
        return;
    }
    
    fetch('{{ route("admin.students.import.preview") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        
        if (data.success) {
            previewData = data.data;
            validationResults = data.validation || null;
            showPreviewStep();
            populatePreviewTable();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses file: ' + error.message);
    })
    .finally(() => {
        const previewBtn = document.getElementById('previewBtn');
        if (previewBtn) {
            previewBtn.innerHTML = '<i class="fas fa-eye me-2"></i>Preview Data';
            previewBtn.disabled = false;
        }
    });
}

function showPreviewStep() {
    currentStep = 2;
    updateStepIndicator();
    document.getElementById('uploadStep').style.display = 'none';
    document.getElementById('previewStep').style.display = 'block';
}

function populatePreviewTable() {
    
    const tbody = document.getElementById('previewTableBody');
    const summary = document.getElementById('importSummary');
    
    if (!tbody) {
        console.error('Preview table body not found');
        return;
    }
    
    tbody.innerHTML = '';
    
    let totalRows = previewData.length;
    let validRows = 0;
    let invalidRows = 0;
    let duplicateRows = 0;
    
    
    previewData.forEach((row, index) => {
        const tr = document.createElement('tr');
        
        // Get validation results for this row
        let isValid = true;
        let statusBadge = '<span class="badge bg-success">Valid</span>';
        let errors = [];
        let warnings = [];
        
        if (validationResults) {
            // Check if row is in invalid list
            const invalidRow = validationResults.invalid.find(r => r.row === index + 1);
            if (invalidRow) {
                isValid = false;
                statusBadge = '<span class="badge bg-danger">Invalid</span>';
                errors = invalidRow.errors || [];
                warnings = invalidRow.warnings || [];
                invalidRows++;
            } else {
                // Check if row is in valid list
                const validRow = validationResults.valid.find(r => r.row === index + 1);
                if (validRow) {
                    warnings = validRow.warnings || [];
                    validRows++;
                } else {
                    // Fallback to basic validation
                    if (!row[1] || !row[2]) {
            isValid = false;
                        statusBadge = '<span class="badge bg-danger">Invalid</span>';
                        errors.push('Data tidak lengkap');
                        invalidRows++;
                    } else {
                        validRows++;
                    }
                }
            }
            
            // Check for duplicates
            const duplicateRow = validationResults.duplicates.find(r => r.row === index + 1);
            if (duplicateRow) {
                duplicateRows++;
        if (isValid) {
                    statusBadge = '<span class="badge bg-warning">Warning</span>';
                }
            }
        } else {
            // Fallback to basic validation
            if (!row[1] || !row[2]) {
                isValid = false;
                statusBadge = '<span class="badge bg-danger">Invalid</span>';
                errors.push('Data tidak lengkap');
            invalidRows++;
            } else {
                validRows++;
            }
        }
        
        // Create error/warning tooltip
        let tooltipContent = '';
        if (errors.length > 0) {
            tooltipContent += '<strong>Errors:</strong><br>' + errors.join('<br>');
        }
        if (warnings.length > 0) {
            if (tooltipContent) tooltipContent += '<br><br>';
            tooltipContent += '<strong>Warnings:</strong><br>' + warnings.join('<br>');
        }
        
        let statusCell = statusBadge;
        if (tooltipContent) {
            statusCell = `<span class="badge ${isValid ? 'bg-warning' : 'bg-danger'}" data-bs-toggle="tooltip" data-bs-html="true" title="${tooltipContent}">${isValid ? 'Warning' : 'Invalid'}</span>`;
        }
        
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${row[0] || '-'}</td>
            <td>${row[1] || '-'}</td>
            <td>${row[2] || '-'}</td>
            <td>${row[3] || '-'}</td>
            <td>${row[5] || 'Aktif'}</td>
            <td>${row[4] || 'Auto Generate'}</td>
            <td>${statusCell}</td>
        `;
        
        tbody.appendChild(tr);
    });
    
    // Update summary
    if (summary) {
    summary.innerHTML = `
        <div class="summary-item">
            <span class="summary-label">Total Data:</span>
            <span class="summary-value">${totalRows}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Data Valid:</span>
            <span class="summary-value text-success">${validRows}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Data Invalid:</span>
            <span class="summary-value text-danger">${invalidRows}</span>
        </div>
            <div class="summary-item">
                <span class="summary-label">Duplicates:</span>
                <span class="summary-value text-warning">${duplicateRows}</span>
            </div>
        `;
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
}

function backToUpload() {
    currentStep = 1;
    updateStepIndicator();
    document.getElementById('previewStep').style.display = 'none';
    document.getElementById('uploadStep').style.display = 'block';
}

// Import button click
document.getElementById('importBtn').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin mengimport data ini?')) {
        importData();
    }
});

function importData() {
    currentStep = 3;
    updateStepIndicator();
    document.getElementById('previewStep').style.display = 'none';
    document.getElementById('importStep').style.display = 'block';
    
    const formData = new FormData();
    formData.append('file', fileData);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('{{ route("admin.students.import.process") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            window.location.href = '{{ route("admin.students.index") }}';
        } else {
            throw new Error('Import failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengimport data');
        backToUpload();
    });
}

function updateStepIndicator() {
    // Reset all steps
    document.querySelectorAll('.step').forEach(step => {
        step.classList.remove('active', 'completed');
    });
    
    // Update current and previous steps
    for (let i = 1; i <= currentStep; i++) {
        const step = document.getElementById(`step${i}`);
        if (i < currentStep) {
            step.classList.add('completed');
        } else if (i === currentStep) {
            step.classList.add('active');
        }
    }
}
</script>
@endpush
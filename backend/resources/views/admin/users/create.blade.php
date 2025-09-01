@extends('admin.layouts.app')

@section('title', 'Tambah Pengguna Baru')

@push('styles')
<style>
.form-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.btn-modern {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.required-field::after {
    content: " *";
    color: #ef4444;
    font-weight: bold;
}

.icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.role-card {
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.role-card:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.role-card.selected {
    border-color: #3b82f6;
    background: #dbeafe;
}

.role-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.password-strength {
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.strength-bar {
    height: 4px;
    border-radius: 2px;
    transition: all 0.3s ease;
    margin-top: 0.25rem;
}

.strength-weak { background-color: #ef4444; width: 25%; }
.strength-fair { background-color: #f59e0b; width: 50%; }
.strength-good { background-color: #3b82f6; width: 75%; }
.strength-strong { background-color: #10b981; width: 100%; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">👤 Tambah Pengguna Baru</h1>
                    <p class="text-muted mb-0">Buat akun pengguna baru untuk sistem</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Pengguna
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="fas fa-user-plus text-white" style="font-size: 1.25rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Formulir Pengguna Baru</h5>
                            <small class="text-white opacity-75">Lengkapi informasi pengguna dengan benar</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 mb-4" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-danger me-3"></i>
                                <div>
                                    <h6 class="mb-1 text-danger">Terdapat Kesalahan!</h6>
                                    <ul class="mb-0 text-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <!-- Personal Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-user me-2"></i>Informasi Personal
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required-field">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           placeholder="Masukkan nama lengkap"
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label required-field">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="contoh@email.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-lock me-2"></i>Keamanan Akun
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label required-field">Kata Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="Minimal 8 karakter"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Kekuatan kata sandi:</small>
                                        <small id="strengthText" class="text-muted">-</small>
                                    </div>
                                    <div class="strength-bar bg-light" id="strengthBar"></div>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label required-field">Konfirmasi Kata Sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="Ulangi kata sandi"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-user-tag me-2"></i>Peran Pengguna
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label required-field">Pilih Peran</label>
                                <div class="row">
                                    @foreach($roles as $role)
                                        <div class="col-md-4 mb-3">
                                            <label class="role-card" for="role_{{ $role->id }}">
                                                <input type="radio" 
                                                       id="role_{{ $role->id }}" 
                                                       name="role_id" 
                                                       value="{{ $role->id }}"
                                                       {{ old('role_id') == $role->id ? 'checked' : '' }}
                                                       required>
                                                <div class="text-center">
                                                    <div class="mb-2">
                                                        @switch($role->role_name)
                                                            @case('admin')
                                                                <i class="fas fa-user-shield text-danger" style="font-size: 2rem;"></i>
                                                                @break
                                                            @case('kepala sekolah')
                                                                <i class="fas fa-crown text-warning" style="font-size: 2rem;"></i>
                                                                @break
                                                            @case('waka kurikulum')
                                                                <i class="fas fa-user-tie text-info" style="font-size: 2rem;"></i>
                                                                @break
                                                            @case('guru')
                                                                <i class="fas fa-chalkboard-teacher text-success" style="font-size: 2rem;"></i>
                                                                @break
                                                            @case('pegawai')
                                                                <i class="fas fa-user-cog text-secondary" style="font-size: 2rem;"></i>
                                                                @break
                                                            @case('siswa')
                                                                <i class="fas fa-graduation-cap text-primary" style="font-size: 2rem;"></i>
                                                                @break
                                                            @default
                                                                <i class="fas fa-user text-secondary" style="font-size: 2rem;"></i>
                                                        @endswitch
                                                    </div>
                                                    <div class="fw-semibold">{{ ucfirst($role->role_name) }}</div>
                                                    <small class="text-muted">
                                                        @switch($role->role_name)
                                                            @case('admin')
                                                                Akses penuh sistem
                                                                @break
                                                            @case('kepala sekolah')
                                                                Pimpinan sekolah
                                                                @break
                                                            @case('waka kurikulum')
                                                                Wakil kepala kurikulum
                                                                @break
                                                            @case('guru')
                                                                Tenaga pengajar
                                                                @break
                                                            @case('pegawai')
                                                                Staff administrasi
                                                                @break
                                                            @case('siswa')
                                                                Peserta didik
                                                                @break
                                                            @default
                                                                Pengguna umum
                                                        @endswitch
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('role_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-modern">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-modern">
                                        <i class="fas fa-save me-2"></i>Simpan Pengguna
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        let strength = 0;
        let text = 'Lemah';
        let className = 'strength-weak';
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        switch(strength) {
            case 0:
            case 1:
                text = 'Sangat Lemah';
                className = 'strength-weak';
                break;
            case 2:
                text = 'Lemah';
                className = 'strength-weak';
                break;
            case 3:
                text = 'Sedang';
                className = 'strength-fair';
                break;
            case 4:
                text = 'Kuat';
                className = 'strength-good';
                break;
            case 5:
                text = 'Sangat Kuat';
                className = 'strength-strong';
                break;
        }
        
        strengthBar.className = 'strength-bar ' + className;
        strengthText.textContent = text;
        strengthText.className = className.replace('strength-', 'text-');
    });

    // Role selection
    document.querySelectorAll('input[name="role_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.role-card').forEach(function(card) {
                card.classList.remove('selected');
            });
            
            // Add selected class to current card
            if (this.checked) {
                this.closest('.role-card').classList.add('selected');
            }
        });
        
        // Set initial selection
        if (radio.checked) {
            radio.closest('.role-card').classList.add('selected');
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Kata sandi dan konfirmasi kata sandi tidak cocok!');
            return;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('Kata sandi minimal harus 8 karakter!');
            return;
        }
    });
});
</script>
@endpush
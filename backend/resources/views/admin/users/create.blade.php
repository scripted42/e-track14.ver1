@extends('admin.layouts.app')

@section('title', 'Tambah Staff Baru')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Staff Baru</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">NIP/NIK</label>
                            <input type="text" name="nip_nik" class="form-control" value="{{ old('nip_nik') }}">
                            @error('nip_nik')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            @error('photo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ old('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                            @error('password_confirmation')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Peran Staff <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control" required>
                                <option value="">Pilih Peran</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->role_name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3" id="walikelas-section" style="display: none;">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_walikelas" id="is_walikelas" value="1" {{ old('is_walikelas') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_walikelas">
                            Sebagai Walikelas
                        </label>
                    </div>
                    <div id="class-room-section" style="display: none;">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="class_room_id" class="form-control">
                            <option value="">Pilih Kelas</option>
                            @foreach($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" {{ old('class_room_id') == $classRoom->id ? 'selected' : '' }}>
                                    {{ $classRoom->name }} - {{ $classRoom->description }}
                                    @if($classRoom->walikelas)
                                        (Sudah ada walikelas: {{ $classRoom->walikelas->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('class_room_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    @error('is_walikelas')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role_id"]');
    const walikelasSection = document.getElementById('walikelas-section');
    const walikelasCheckbox = document.getElementById('is_walikelas');
    const classRoomSection = document.getElementById('class-room-section');
    
    // Function to toggle walikelas section
    function toggleWalikelasSection() {
        const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
        if (selectedRole.includes('guru')) {
            walikelasSection.style.display = 'block';
        } else {
            walikelasSection.style.display = 'none';
            // Uncheck walikelas checkbox if not guru
            walikelasCheckbox.checked = false;
            classRoomSection.style.display = 'none';
        }
    }
    
    // Function to toggle class room section
    function toggleClassRoomSection() {
        if (walikelasCheckbox.checked) {
            classRoomSection.style.display = 'block';
        } else {
            classRoomSection.style.display = 'none';
        }
    }
    
    // Listen for role changes
    roleSelect.addEventListener('change', toggleWalikelasSection);
    
    // Listen for walikelas checkbox changes
    walikelasCheckbox.addEventListener('change', toggleClassRoomSection);
    
    // Check on page load if there's an old value
    if (roleSelect.value) {
        toggleWalikelasSection();
    }
    
    if (walikelasCheckbox.checked) {
        toggleClassRoomSection();
    }
});
</script>
@endpush


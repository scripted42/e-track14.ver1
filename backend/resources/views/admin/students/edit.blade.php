@extends('admin.layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Siswa</h1>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" class="form-control" required value="{{ old('nisn', $student->nisn) }}">
                            @error('nisn')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $student->name) }}">
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
                            @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $student->photo) }}" 
                                         alt="Foto" 
                                         class="rounded" 
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         onerror="this.style.display='none';">
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            @error('photo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="class_name" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->name }}" {{ old('class_name', $student->class_name) == $class->name ? 'selected' : '' }}>
                                        {{ $class->name }} - {{ $class->level }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $student->address) }}</textarea>
                    @error('address')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">QR Kartu Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="card_qr_code" class="form-control" required value="{{ old('card_qr_code', $student->card_qr_code) }}">
                            @error('card_qr_code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="Aktif" {{ old('status', $student->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Non-Aktif" {{ old('status', $student->status) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                <option value="Lulus" {{ old('status', $student->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="Pindah" {{ old('status', $student->status) == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                                <option value="Drop Out" {{ old('status', $student->status) == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                                <option value="Tidak Naik Kelas" {{ old('status', $student->status) == 'Tidak Naik Kelas' ? 'selected' : '' }}>Tidak Naik Kelas</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection




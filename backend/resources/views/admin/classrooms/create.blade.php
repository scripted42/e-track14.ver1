@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Kelas</h1>
        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.classrooms.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="Contoh: 7A, 8B, 9C">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select name="level" class="form-control" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="7" {{ old('level') == '7' ? 'selected' : '' }}>Kelas 7</option>
                                <option value="8" {{ old('level') == '8' ? 'selected' : '' }}>Kelas 8</option>
                                <option value="9" {{ old('level') == '9' ? 'selected' : '' }}>Kelas 9</option>
                            </select>
                            @error('level')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi kelas (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Walikelas</label>
                    <select name="walikelas_id" class="form-control">
                        <option value="">Pilih Walikelas (Opsional)</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('walikelas_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} - {{ $teacher->nip_nik ?? 'N/A' }}
                                @if($teacher->classRoom)
                                    (Sudah walikelas: {{ $teacher->classRoom->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('walikelas_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Kelas Aktif
                        </label>
                    </div>
                    @error('is_active')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

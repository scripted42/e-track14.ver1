@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Kenaikan Kelas & Kelulusan</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Statistik</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li>Total Aktif: <strong>{{ $stats['total_active_students'] ?? 0 }}</strong></li>
                        <li>Lulus: <strong>{{ $stats['graduated_students'] ?? 0 }}</strong></li>
                        <li>Pindah: <strong>{{ $stats['transferred_students'] ?? 0 }}</strong></li>
                        <li>Drop Out: <strong>{{ $stats['dropout_students'] ?? 0 }}</strong></li>
                        <li>Tidak Naik: <strong>{{ $stats['retained_students'] ?? 0 }}</strong></li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Batch Promotion</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.students.promotion.batch-promotion') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" name="academic_year" value="{{ $academicYear ?? (now()->year.'/'.(now()->year+1)) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tanggal Kelulusan Kelas 9</label>
                            <input type="date" class="form-control" name="graduation_date" value="{{ now()->toDateString() }}">
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Proses Batch</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Kenaikan Kelas</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.students.promotion.promote-class') }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Dari Kelas</label>
                            <select name="from_class" class="form-select">
                                @foreach(array_keys($classDistribution ?? []) as $className)
                                    <option value="{{ $className }}">{{ $className }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ke Kelas</label>
                            <input type="text" class="form-control" name="to_class" placeholder="cth: 8A">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" name="academic_year" value="{{ $academicYear ?? (now()->year.'/'.(now()->year+1)) }}">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-success" type="submit">Promosikan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Kelulusan</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.students.promotion.graduate-class') }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Kelas 9</label>
                            <select name="class_name" class="form-select">
                                @foreach(array_keys($classDistribution ?? []) as $className)
                                    @php $isClass9 = is_string($className) && substr($className, 0, 1) === '9'; @endphp
                                    @if($isClass9)
                                        <option value="{{ $className }}">{{ $className }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Kelulusan</label>
                            <input type="date" class="form-control" name="graduation_date" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" name="academic_year" value="{{ $academicYear ?? (now()->year.'/'.(now()->year+1)) }}">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-danger" type="submit">Luluskan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



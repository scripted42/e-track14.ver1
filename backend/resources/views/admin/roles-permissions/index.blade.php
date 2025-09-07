@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Role & Izin</h4>
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
                <div class="card-header">Buat Role Baru</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Nama Role</label>
                            <input type="text" name="name" class="form-control" placeholder="cth: Waka Kesiswaan" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Simpan</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Buat Izin Baru</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Nama Izin</label>
                            <input type="text" name="name" class="form-control" placeholder="cth: report.view_kesiswaan" required>
                        </div>
                        <button class="btn btn-secondary w-100" type="submit">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Kelola Izin per Role</div>
                <div class="card-body">
                    @php
                        $permLabels = [
                            'attendance.view_all' => 'Kehadiran: Lihat Semua',
                            'attendance.manage' => 'Kehadiran: Kelola',
                            'leave.approve' => 'Izin: Setujui',
                            'leave.reject' => 'Izin: Tolak',
                            'leave.view_all' => 'Izin: Lihat Semua',
                            'report.view' => 'Laporan: Lihat (Per Peran)',
                            'report.view_all' => 'Laporan: Lihat Semua',
                            'student.manage' => 'Siswa: Kelola',
                            'staff.manage' => 'Staff: Kelola',
                            'settings.manage' => 'Pengaturan: Kelola',
                        ];
                        $toLabel = function($name) use ($permLabels) {
                            return $permLabels[$name] ?? ucwords(str_replace(['.', '_'], [': ', ' '], $name));
                        };
                        // Kelompokkan izin berdasarkan prefix
                        $groupLabels = [
                            'attendance' => 'Kehadiran',
                            'leave' => 'Izin/Cuti',
                            'report' => 'Laporan',
                            'student' => 'Siswa',
                            'staff' => 'Staff',
                            'settings' => 'Pengaturan',
                            'lainnya' => 'Lainnya',
                        ];
                        $grouped = [];
                        foreach ($permissions as $p) {
                            $parts = explode('.', $p->name);
                            $key = $parts[0] ?? 'lainnya';
                            $grouped[$key] = $grouped[$key] ?? [];
                            $grouped[$key][] = $p;
                        }
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 18%">Role</th>
                                    <th>Izin</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    @php $formId = 'rp_form_'.$role->id; @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $role->name }}</strong>
                                            @if(in_array($role->name, $systemRoles))
                                                <span class="badge bg-info ms-1">Sistem</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form id="{{ $formId }}" method="POST" action="{{ route('admin.roles.permissions.update', $role) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="d-flex gap-2 mb-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPerms('{{ $formId }}', true)">Pilih Semua</button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPerms('{{ $formId }}', false)">Kosongkan</button>
                                                </div>
                                                <div class="row g-3">
                                                    @foreach($grouped as $gkey => $items)
                                                        <div class="col-md-6">
                                                            <div class="border rounded p-2 h-100">
                                                                <div class="fw-semibold mb-2">{{ $groupLabels[$gkey] ?? ucfirst($gkey) }}</div>
                                                                @foreach($items as $perm)
                                                                    <div class="form-check mb-1">
                                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $role->id }}_{{ $perm->id }}" {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="perm_{{ $role->id }}_{{ $perm->id }}">{{ $toLabel($perm->name) }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="mt-3">
                                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td style="white-space:nowrap;">
                                            @if(!in_array($role->name, $systemRoles))
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <script>
                        function toggleAllPerms(formId, checked) {
                            var form = document.getElementById(formId);
                            if (!form) return;
                            var inputs = form.querySelectorAll('input.form-check-input[type="checkbox"]');
                            inputs.forEach(function(el){ el.checked = !!checked; });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



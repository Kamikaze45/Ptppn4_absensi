@extends('layouts.app')
@section('page-title', 'Daftar Pegawai')
@section('title', 'Daftar Pegawai')

@section('content')
<div class="page-header">
    <div>
        <h1>Daftar Pegawai</h1>
        <p>Kelola data pegawai PTPN4 Regional 7</p>
    </div>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Tambah Pegawai
    </a>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 24px;">
        <form method="GET" action="{{ route('employees.index') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--gray);"></i>
                    <input type="text" name="search" placeholder="Cari nama atau NIP..." value="{{ $search }}"
                        class="form-control" style="padding-left:38px;">
                </div>
            </div>
            <div>
                <select name="jabatan" class="form-control" style="width:auto;">
                    <option value="">Semua Jabatan</option>
                    @foreach(\App\Models\User::jabatanList() as $key => $val)
                    <option value="{{ $key }}" {{ $jabatan === $key ? 'selected' : '' }}>{{ $val }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Cari</button>
            @if($search || $jabatan)
            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Reset</a>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pegawai</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Status</th>
                        <th>Bergabung</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;border-radius:50%;background:var(--green-pale);border:2px solid var(--green-pale2);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--green-deep);font-size:15px;flex-shrink:0;overflow:hidden;">
                                    @if($emp->photo)
                                        <img src="{{ Storage::url($emp->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;">{{ $emp->name }}</div>
                                    <div style="font-size:12px;color:var(--gray);">{{ $emp->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span style="font-family:'Sora',sans-serif;font-weight:600;font-size:13px;">{{ $emp->nip }}</span></td>
                        <td style="font-size:13px;">{{ $emp->jabatan ?? '-' }}</td>
                        <td style="font-size:13px;">{{ $emp->departemen ?? '-' }}</td>
                        <td>
                            @if($emp->status === 'aktif')
                            <span class="badge badge-hadir"><span class="pulse-dot" style="background:#16a34a;"></span> Aktif</span>
                            @else
                            <span class="badge badge-absent">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--gray);">{{ $emp->join_date ? \Carbon\Carbon::parse($emp->join_date)->format('d M Y') : '-' }}</td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete({{ $emp->id }}, '{{ $emp->name }}')" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <p>{{ $search ? 'Tidak ditemukan pegawai dengan kata kunci "' . $search . '"' : 'Belum ada data pegawai' }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div style="padding:16px;border-top:1px solid var(--border);">
            {{ $employees->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:20px;padding:32px;max-width:380px;width:90%;text-align:center;">
        <div style="font-size:48px;margin-bottom:16px;">🗑️</div>
        <div style="font-family:'Sora',sans-serif;font-size:18px;font-weight:700;margin-bottom:8px;">Hapus Pegawai?</div>
        <div style="font-size:14px;color:var(--gray);margin-bottom:24px;" id="deleteMsg"></div>
        <form id="deleteForm" method="POST" style="display:flex;gap:10px;">
            @csrf @method('DELETE')
            <button type="button" onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-secondary" style="flex:1;">Batal</button>
            <button type="submit" class="btn btn-danger" style="flex:1;"><i class="fas fa-trash"></i> Hapus</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteMsg').textContent = 'Data pegawai "' + name + '" akan dihapus permanen.';
    document.getElementById('deleteForm').action = '/pegawai/' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
</script>
@endpush

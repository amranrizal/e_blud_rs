@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Manajemen User</h6>
        {{-- Tombol Tambah --}}
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus"></i> Tambah User
        </a>
    </div>
    <div class="card-body">
        
        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama User</th>
                        <th>Role (Hak Akses)</th>
                        <th>Unit Kerja</th> {{-- HEADER ADA --}}
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>
                        
                        {{-- KOLOM ROLE --}}
                        <td>
                            @php $role = strtolower(trim($user->role)); @endphp
                            @if($role == 'admin')
                                <span class="badge badge-danger">ADMIN KEUANGAN</span>
                            @elseif($role == 'verifikator')
                                <span class="badge badge-warning text-dark">VERIFIKATOR</span>
                            @elseif($role == 'user')
                                <span class="badge badge-success">USER PELAKSANA</span>
                            @else
                                <span class="badge badge-secondary">{{ strtoupper($role) }}</span>
                            @endif
                        </td>

                        {{-- KOLOM UNIT KERJA (INI YANG PENTING) --}}
                        <td>
                            {{-- Cek apakah User punya relasi unitKerja? --}}
                            @if($user->unitKerja)
                                
                                {{-- TAMPILKAN NAMA UNIT --}}
                                <span class="font-weight-bold text-dark">
                                    {{ $user->unitKerja->nama_unit }}
                                </span>
                                <br>
                                {{-- Tampilkan Kode kecil di bawahnya biar informatif --}}
                                <small class="text-muted">Kode: {{ $user->kode_unit }}</small>

                            @else
                                
                                {{-- Kalau tidak ada relasi (Misal Admin) --}}
                                <span class="badge badge-secondary">Admin / Non-Unit</span>
                                
                            @endif
                        </td>

                        {{-- KOLOM AKSI --}}
                        <td class="text-center">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            
                            {{-- Form Hapus --}}
                            @if(auth()->id() !== $user->id) {{-- Cek biar gak hapus diri sendiri --}}
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">Belum ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
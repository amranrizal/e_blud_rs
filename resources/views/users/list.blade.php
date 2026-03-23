@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Manajemen User (File List)</h6>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Tambah User</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>Role (Status)</th> <!-- Kita fokus kesini -->
                        <th>Unit Kerja</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        {{-- KOLOM 1: NAMA --}}
                        <td>
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>

                        {{-- KOLOM 2: ROLE --}}
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

                        {{-- KOLOM 3: UNIT KERJA (INI YANG TADI HILANG) --}}
                        <td>
                            @if($user->kode_unit)
                                <span class="badge badge-info">{{ $user->kode_unit }}</span>
                                {{-- Jika ingin nama unit, nanti kita buat relasi di Model --}}
                            @else
                                <span class="text-muted font-italic">- Tidak Ada -</span>
                            @endif
                        </td>

                        {{-- KOLOM 4: AKSI --}}
                        <td class="text-center">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            
                            {{-- Tambahkan Tombol Hapus biar lengkap --}}
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
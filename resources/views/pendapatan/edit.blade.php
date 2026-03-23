@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data User</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting! Untuk mengubah method POST jadi PUT --}}
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label>Email (Username)</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                <small class="text-muted">Isi hanya jika ingin mereset password user ini.</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Super Admin (Keuangan)</option>
                            <option value="verifikator" {{ $user->role == 'verifikator' ? 'selected' : '' }}>Verifikator</option>
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User Pelaksana (Unit)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Unit Kerja</label>
                        <select name="kode_unit" class="form-control select2" id="selectUnit">
                            <option value="">-- Kosong (Khusus Admin) --</option>
                            
                            @foreach($units as $u)
                                {{-- PERHATIKAN VALUE-NYA: --}}
                                {{-- JANGAN value="{{ $u->id }}" --}}
                                {{-- HARUS value="{{ $u->kode_unit }}" --}}
                                
                                <option value="{{ $u->kode_unit }}" 
                                    {{-- LOGIC SELECTED: Bandingkan Kode dengan Kode --}}
                                    {{ (string)$user->kode_unit === (string)$u->kode_unit ? 'selected' : '' }}>
                                    
                                    {{ $u->kode_unit }} - {{ $u->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pastikan memilih Unit yang benar (sesuai Kode).</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update User</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
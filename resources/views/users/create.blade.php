@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah User Baru</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email (Username)</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required id="selectRole">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Super Admin (Keuangan)</option>
                            <option value="pimpinan">Pimpinan (Approver)</option>
                            <option value="verifikator">Verifikator</option>
                            <option value="user">User Pelaksana (Unit)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Unit Kerja</label>
                        <select name="kode_unit" class="form-control" id="selectUnit">
                            <option value="">-- Kosong (Khusus Admin) --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->kode_unit }}">{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Admin tidak butuh unit. User wajib pilih unit.</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Data</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        </form>
    </div>
</div>
@endsection
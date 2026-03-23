@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- HEADER & TOMBOL TAMBAH -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 font-weight-bold text-primary">🏢 Master Unit Kerja (OPD)</h6>
            
            {{-- TOMBOL TAMBAH (Hanya Admin) --}}
            @if(auth()->user()->role === 'admin')
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
                <i class="fas fa-plus-circle"></i> Tambah Unit
            </button>
            @endif
        </div>

        <div class="card-body">
            <!-- ALERT NOTIFIKASI -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- TABEL DATA -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Kode Unit</th>
                            <th>Nama Unit Kerja</th>
                            @if(auth()->user()->role === 'admin')
                                <th width="15%" class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td><span class="badge badge-secondary">{{ $unit->kode_unit }}</span></td>
                            <td class="font-weight-bold">{{ $unit->nama_unit }}</td>
                            
                            @if(auth()->user()->role === 'admin')
                            <td class="text-center">
                                <!-- Tombol Edit (Trigger Modal) -->
                                <button class="btn btn-sm btn-warning text-white" 
                                        data-toggle="modal" 
                                        data-target="#modalEdit{{ $unit->id }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                
                                <!-- Tombol Hapus -->
                                <form action="{{ route('master.unit.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus unit ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>

                        <!-- ==========================
                             MODAL EDIT (Di dalam Loop) 
                             ========================== -->
                        <div class="modal fade" id="modalEdit{{ $unit->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title font-weight-bold">Edit Unit Kerja</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('master.unit.update', $unit->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Kode Unit</label>
                                                <input type="text" name="kode_unit" class="form-control" value="{{ $unit->kode_unit }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Nama Unit</label>
                                                <input type="text" name="nama_unit" class="form-control" value="{{ $unit->nama_unit }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal Edit -->

                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada data unit kerja.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ==========================
     MODAL TAMBAH (Di Luar Loop) 
     ========================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Tambah Unit Kerja Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('master.unit.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Kode Unit</label>
                        <input type="text" name="kode_unit" class="form-control" placeholder="Contoh: 1.02.01" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Unit</label>
                        <input type="text" name="nama_unit" class="form-control" placeholder="Contoh: RSUD K.H. HAYYUNG" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
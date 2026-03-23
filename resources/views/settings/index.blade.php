@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="fas fa-cogs me-2"></i> Pengaturan Sistem</h3>
    </div>

    <!-- TABS NAVIGASI -->
    <ul class="nav nav-tabs mb-4" id="settingTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active fw-bold"
            id="instansi-tab"
            data-toggle="tab"
            href="#instansi"
            role="tab">
                <i class="fas fa-building me-1"></i> Profil Instansi
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link fw-bold"
            id="pejabat-tab"
            data-toggle="tab"
            href="#pejabat"
            role="tab">
                <i class="fas fa-user-tie me-1"></i> Data Pejabat (TTD)
            </a>
        </li>
    </ul>

    <!-- ISI CONTENT -->
    <div class="tab-content" id="settingTabsContent">
        
        <!-- TAB 1: PROFIL INSTANSI (TIDAK BERUBAH) -->
        <div class="tab-pane fade show active" id="instansi" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('settings.instansi.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <label class="form-label fw-bold d-block">Logo Instansi</label>
                                <div class="mb-3">
                                    <img src="{{ asset('img/' . ($instansi->logo_path ?? 'default.png')) }}" 
                                         class="img-thumbnail rounded-circle shadow-sm" width="150" height="150"
                                         onerror="this.src='https://via.placeholder.com/150?text=No+Logo'">
                                </div>
                                <input type="file" name="logo" class="form-control form-control-sm">
                                <small class="text-muted">Format: PNG/JPG. Max 2MB.</small>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Header Pemerintah (Kabupaten/Provinsi)</label>
                                    <input type="text" name="kabupaten" class="form-control" value="{{ $instansi->kabupaten ?? '' }}" placeholder="PEMERINTAH KABUPATEN ...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Instansi / SKPD</label>
                                    <input type="text" name="nama_instansi" class="form-control" value="{{ $instansi->nama_instansi ?? '' }}" placeholder="RSUD ...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="3">{{ $instansi->alamat ?? '' }}</textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary fw-bold">
                                        <i class="fas fa-save me-1"></i> Simpan Profil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TAB 2: DATA PEJABAT -->
        <div class="tab-pane fade" id="pejabat" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0 fw-bold">Daftar Pejabat Penandatangan</h6>
                    <button class="btn btn-success btn-sm shadow-sm"data-toggle="modal"data-target="#modalAddPejabat">
                        <i class="fas fa-plus me-1"></i> Tambah Pejabat
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Lengkap</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <!-- [BARU] Kolom Unit Kerja -->
                                <th>Unit Kerja</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pejabat as $p)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $p->nama }}</td>
                                <td class="font-monospace text-muted">{{ $p->nip }}</td>
                                <td><span class="badge bg-info text-dark">{{ $p->jabatan }}</span></td>
                                
                                <!-- [BARU] Menampilkan Nama Unit (Butuh relasi di model Pejabat) -->
                                <td>
                                    @if($p->unit)
                                        <small class="text-muted fw-bold"><i class="fas fa-building me-1"></i> {{ $p->unit->nama_unit }}</small>
                                    @else
                                        <span class="badge bg-secondary">Umum / Semua Unit</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                   <button class="btn btn-warning btn-sm btn-edit-pejabat"data-json='@json($p)'data-toggle="modal"data-target="#modalEditPejabat">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <form action="{{ route('settings.pejabat.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pejabat ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data pejabat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PEJABAT -->
<div class="modal fade" id="modalAddPejabat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tambah Pejabat</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('settings.pejabat.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- [BARU] Dropdown Unit Kerja -->
                    <div class="mb-3">
                        <label class="fw-bold small">Unit Kerja / Bagian</label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                            @endforeach
                            <!-- Opsi NULL jika pejabat lintas sektor -->
                            <!-- <option value="">Tidak Ada / Umum</option> -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small">Nama Lengkap (Gelar)</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Contoh: Dr. Budi, Sp.PD">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">NIP</label>
                        <input type="text" name="nip" class="form-control" required placeholder="198xxxx...">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" required placeholder="Contoh: Direktur / PPTK">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT PEJABAT -->
<div class="modal fade" id="modalEditPejabat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Edit Pejabat</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formEditPejabat" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    
                    <!-- [BARU] Dropdown Unit di Edit -->
                    <div class="mb-3">
                        <label class="fw-bold small">Unit Kerja / Bagian</label>
                        <select name="unit_id" id="edit_unit_id" class="form-select" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->kode_unit }} - {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small">Nama Lengkap (Gelar)</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">NIP</label>
                        <input type="text" name="nip" id="edit_nip" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">Jabatan</label>
                        <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // [BARU] Script Edit: Mengisi Data ke Modal termasuk Select Unit
    $('.btn-edit-pejabat').click(function() {
        let data = $(this).data('json');
        
        $('#edit_nama').val(data.nama);
        $('#edit_nip').val(data.nip);
        $('#edit_jabatan').val(data.jabatan);
        
        // Mengisi Select Unit
        // Pastikan value option sesuai dengan id unit di data json
        $('#edit_unit_id').val(data.unit_id); 
        
        // Update URL Form Action
        let url = "{{ route('settings.pejabat.update', ':id') }}";
        url = url.replace(':id', data.id);
        $('#formEditPejabat').attr('action', url);
    });
</script>
@endpush
@extends('layouts.app')

@section('content')
<style>
    /* Menyembunyikan Alert bawaan Layout secara paksa */
    .alert.alert-success {
        display: none !important;
    }
</style>
<!-- CSS Select2 (Taruh di atas aman) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />

<div class="container-fluid">
    <!-- Header -->
    <a href="{{ route('pagu.index', ['tahun' => $tahun]) }}" class="text-decoration-none text-muted mb-3 d-inline-block">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Unit
    </a>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Mapping Kegiatan: {{ $unit->nama_unit }}</h3>
            <span class="badge bg-info text-dark">Tahun Anggaran {{ $tahun }}</span>
        </div>
        <div class="text-end">
            <small class="text-muted d-block">Total Pagu Termapping</small>
            <h3 class="fw-bold text-success mb-0">
                Rp {{ number_format($pagus->sum('pagu'), 0, ',', '.') }}
            </h3>
        </div>
    </div>
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="row">
        <!-- FORM INPUT (Kiri) -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-plus-circle text-primary"></i> Tambah Kegiatan
                </div>
                <div class="card-body">
                    <form action="{{ route('pagu.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Sub Kegiatan</label>
                            
                            <!-- INPUT SELECT (ID = select-kegiatan) -->
                            <select name="sub_kegiatan_id"
                                    id="select-kegiatan"
                                    class="form-control select2"
                                    style="width:100%;"
                                    required>
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($subKegiatans as $sub)
                                    <option value="{{ $sub->id }}">
                                        {{ $sub->kode_program }} - {{ $sub->nama_program }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pagu Indikatif (Batas Atas)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="pagu" class="form-control" placeholder="0" min="0" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Simpan Mapping
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABEL LIST (Kanan) -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Sub Kegiatan</th>
                                <th class="text-end">Pagu (Rp)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagus as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ optional($item->subKegiatan)->kode_program ?? '-' }}</div>
                                    <small>{{Str::limit($item->subKegiatan->nama_program ?? '-', 80)}}</small>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($item->pagu, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <!-- KODE DEBUG 
                                <tr>
                                    <td colspan="5" class="bg-danger text-white">
                                        <strong>DEBUG DATA ITEM:</strong><br>
                                        {{ json_encode($item) }}
                                    </td>
                                </tr>-->
                                    
                                    <!-- Tombol Edit Pagu -->
                                    @php
                                        // 1. Ambil Objek Relasi (Bisa jadi 'subKegiatan' atau 'sub_kegiatan')
                                        $relasi = $item->subKegiatan;

                                        // 2. Ambil Kolom Spesifik (Sesuai JSON Anda: kode_program & nama_program)
                                        $kodeFix = $relasi->kode_program ?? '-';
                                        $namaFix = $relasi->nama_program ?? '-';
                                    @endphp

                                    <button type="button" 
                                        class="btn btn-sm btn-light text-warning btn-edit-mapping" 
                                        title="Edit Pagu"
                                        data-id="{{ $item->id }}"
                                        data-pagu="{{ $item->pagu }}" 
                                        data-kode="{{ $kodeFix }}"
                                        data-nama="{{ $namaFix }}"
                                        data-toggle="modal" 
                                        data-target="#modalEditMapping">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    
                                    <form action="{{ route('pagu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pagu (Revisi) -->
<div class="modal fade" id="modalEditMapping" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Edit Pagu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formEditMapping" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <!-- Kode Sub Kegiatan -->
                    <div class="mb-2">
                        <label class="small text-muted fw-bold">Kode Sub Kegiatan</label>
                        <input type="text" class="form-control form-control-sm bg-light fw-bold" id="edit_kode" readonly>
                    </div>

                    <!-- Nama Sub Kegiatan -->
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Nama Sub Kegiatan</label>
                        <textarea class="form-control bg-light" id="edit_nama" rows="2" readonly></textarea>
                    </div>

                    <!-- Input Pagu -->
                    <div class="mb-3">
                        <label class="fw-bold">Pagu Indikatif (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="pagu" id="edit_pagu" class="form-control fw-bold text-primary" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- INI KUNCINYA: Mendorong Script ke Footer Layout -->
@push('scripts')
<!-- 1. WAJIB LOAD LIBRARY SWEETALERT DULU -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    function sipdMatcher(params, data) {

        if ($.trim(params.term) === '') {
            return data;
        }

        if (typeof data.text === 'undefined') {
            return null;
        }

        let keyword = params.term.toLowerCase();

        let text = data.text.toLowerCase();

        // hilangkan titik
        let textNoDot = text.replace(/\./g,'');

        let keywordNoDot = keyword.replace(/\./g,'');

        // pisahkan kode dan nama
        let parts = text.split('-');

        let kode = parts[0] ? parts[0].trim() : '';
        let nama = parts[1] ? parts[1].trim() : '';

        if(
            text.indexOf(keyword) > -1 ||
            textNoDot.indexOf(keywordNoDot) > -1 ||
            kode.indexOf(keyword) > -1 ||
            nama.indexOf(keyword) > -1
        ){
            return data;
        }

        return null;
    }

    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Pilih Kegiatan --',
        matcher: sipdMatcher
    });

    console.log('Script Edit Ready...');

    // --- A. LOGIC TOMBOL EDIT & MODAL (YANG SUDAH JALAN) ---
    $(document).off('click', '.btn-edit-mapping');
    $(document).on('click', '.btn-edit-mapping', function() {
        let id   = $(this).attr('data-id');
        let kode = $(this).attr('data-kode');
        let nama = $(this).attr('data-nama');
        let pagu = $(this).attr('data-pagu');

        $('#edit_kode').val(kode);
        $('#edit_nama').val(nama);
        $('#edit_pagu').val(pagu);

        let url = "{{ route('pagu.update_nominal', ':id') }}";
        url = url.replace(':id', id);
        $('#formEditMapping').attr('action', url);
    });

    // --- B. LOGIC NOTIFIKASI TOAST (FIX) ---
    // Definisi Toast
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Cek Pesan Sukses dari Controller
    @if(session('success'))
        console.log("Pesan Sukses Diterima: {{ session('success') }}"); // Cek Console
        Toast.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}"
        });
    @endif

    // Cek Pesan Error
    @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}"
        });
    @endif
    
    // Cek Error Validasi (Misal input kosong)
    @if($errors->any())
        Toast.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Cek kembali inputan Anda.'
        });
    @endif

});
</script>
@endpush
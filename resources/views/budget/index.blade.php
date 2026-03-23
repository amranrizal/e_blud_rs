@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header & Filter (TETAP SAMA) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark fw-bold mb-0">
            <img src="https://img.icons8.com/color/48/000000/microsoft-excel-2019--v1.png" width="30" class="me-2">
            Input RKA Belanja
        </h3>
        
        <form method="GET" class="d-flex align-items-center bg-white p-2 rounded shadow-sm">
            @if(request('unit_id')) <input type="hidden" name="unit_id" value="{{ request('unit_id') }}"> @endif
            <label class="me-2 fw-bold small text-muted">Tahun Anggaran:</label>
            <select name="tahun" class="form-select form-select-sm border-0 bg-light fw-bold" style="width: 100px;" onchange="this.form.submit()">
                @for($i = date('Y')-1; $i <= date('Y')+3; $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    <div class="row">
        <!-- Sidebar Kiri (TETAP SAMA) -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-building me-2"></i> Unit Kerja
                </div>
                <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                    @foreach($units as $unit)
                        <a href="{{ route('budget.index', ['unit_id' => $unit->id, 'tahun' => $tahun]) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedUnit && $selectedUnit->id == $unit->id ? 'active fw-bold' : '' }}">
                            <span>{{ $unit->nama_unit }}</span>
                            @if($selectedUnit && $selectedUnit->id == $unit->id)
                                <i class="fas fa-chevron-right"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Konten Kanan -->
        <div class="col-md-9">
            @if($selectedUnit)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">{{ $selectedUnit->nama_unit }}</h5>
                        <small class="text-muted">Daftar Sub Kegiatan & Pagu Indikatif</small>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle" style="font-size: 0.9rem;">
                                <thead class="table-light text-secondary">
                                    <tr>
                                        <th width="5%" class="text-center">Aksi</th>
                                        <th width="45%">Uraian Sub Kegiatan</th>
                                        <th width="10%" class="text-center">Status</th>
                                        <th width="15%" class="text-end">Pagu Validasi</th>
                                        <th width="15%" class="text-end">Rincian Belanja</th>
                                        <th width="10%" class="text-end">Persen</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php
                                        $lastProgram = null;
                                        $lastKegiatan = null;
                                    @endphp

                                    @forelse($kegiatans as $item)

                                        {{-- HEADER PROGRAM & KEGIATAN --}}
                                        @if($item->prog_kode != $lastProgram)
                                        
                                            <tr class="bg-light">
                                                <td></td>
                                                <td colspan="5" class="fw-bold text-dark pt-3 pb-2" style="font-size:1.05em;">
                                                    {{ $item->prog_kode }} {{ $item->prog_nama }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td colspan="5" class="ps-4 pb-3">

                                                    <div class="card border-0 shadow-sm">

                                                        <div class="card-body p-3">

                                                            {{-- HEADER --}}
                                                            <div class="d-flex justify-content-between align-items-center mb-3">

                                                                <div>
                                                                    <h6 class="mb-0 fw-bold text-primary">
                                                                        <i class="fas fa-bullseye me-1"></i>
                                                                        Indikator Program (Outcome)
                                                                    </h6>

                                                                    <small class="text-muted">
                                                                        Target kinerja strategis level program
                                                                    </small>
                                                                </div>

                                                                @if(in_array(strtolower(auth()->user()->role), ['admin','super admin']))
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary"
                                                                        data-toggle="modal"
                                                                        data-target="#modalIndikatorProgram"
                                                                        data-program-id="{{ $item->prog_id }}">
                                                                        <i class="fas fa-plus me-1"></i> Tambah
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            @php
                                                                $indikators = $programIndikators[$item->prog_id] ?? collect();
                                                            @endphp

                                                            {{-- LIST INDIKATOR --}}
                                                            @if($indikators->count() > 0)

                                                            <div class="border rounded overflow-hidden">

                                                                    @foreach($indikators as $ind)                                                               
                                                                        <div class="d-flex justify-content-between align-items-center px-3 py-1 border-bottom indicator-row">

                                                                            <div class="flex-grow-1">
                                                                                <div class="fw-semibold small text-dark">
                                                                                    {{ $ind->tolok_ukur }}
                                                                                </div>

                                                                                <div class="small text-muted">
                                                                                    Target: <strong>{{ $ind->target }}</strong> {{ $ind->satuan }}
                                                                                </div>
                                                                            </div>

                                                                            @if(in_array(strtolower(auth()->user()->role), ['admin','super admin']))
                                                                                <div class="btn-group btn-group-sm ms-2">
                                                                                    <button class="btn btn-outline-warning btnEditIndikator"
                                                                                            data-id="{{ $ind->id }}"
                                                                                            data-tolok="{{ $ind->tolok_ukur }}"
                                                                                            data-target="{{ $ind->target }}"
                                                                                            data-satuan="{{ $ind->satuan }}">
                                                                                        <i class="fas fa-pencil-alt"></i>
                                                                                    </button>

                                                                                    <button class="btn btn-outline-danger btnHapusIndikator"
                                                                                            data-id="{{ $ind->id }}">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            @endif

                                                                        </div>

                                                                    @endforeach

                                                                </div>

                                                            @else

                                                                <div class="alert alert-warning mb-0 py-2 small">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Program ini belum memiliki indikator outcome.
                                                                </div>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </td>
                                            </tr>

                                            @php $lastProgram = $item->prog_kode; $lastKegiatan = null; @endphp
                                        @endif

                                        @if(!isset($lastKegiatan) || $item->keg_kode != $lastKegiatan)

                                            {{-- HEADER KEGIATAN --}}
                                            <tr>
                                                <td></td>
                                                <td colspan="5" class="ps-4 pt-2 pb-2">

                                                    <div class="d-flex justify-content-between align-items-center">

                                                        <div class="fw-bold text-secondary">
                                                            {{ $item->keg_kode }} {{ $item->keg_nama }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- PANEL INDIKATOR KEGIATAN --}}
                                            @php
                                                $indikatorKegiatans = $kegiatanIndikators[$item->keg_id] ?? collect();
                                            @endphp

                                       {{-- @if($indikatorKegiatans->count()) --}}

                                            <tr>
                                                <td></td>
                                                <td colspan="5" class="ps-4 pb-3">

                                                    <div class="card border-0 shadow-sm">

                                                        <div class="card-body p-3">

                                                        {{-- HEADER --}}
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <h6 class="mb-0 text-primary fw-bold">
                                                                    <i class="fas fa-bullseye me-1"></i>
                                                                    Indikator Kegiatan (Output)
                                                                </h6>
                                                                <small class="text-muted">
                                                                    Target kinerja level kegiatan
                                                                </small>
                                                            </div>

                                                            @php
                                                                $isAdmin = in_array(strtolower(auth()->user()->role), ['admin','super admin']);
                                                            @endphp

                                                            @if($isAdmin)

                                                                @if($item->program_has_indikator)
                                                                    <button class="btn btn-primary btn-sm px-3"
                                                                        data-toggle="modal"
                                                                        data-target="#modalIndikatorKegiatan"
                                                                        data-kegiatan-id="{{ $item->keg_id }}">
                                                                        <i class="fas fa-plus me-1"></i> Tambah
                                                                    </button>
                                                                @else
                                                                    <button class="btn btn-sm btn-outline-danger" disabled>
                                                                        <i class="fas fa-lock me-1"></i> Isi indikator program dulu
                                                                    </button>
                                                                @endif

                                                            @endif

                                                        </div>

                                                        {{-- LIST --}}
                                                        @if($indikatorKegiatans->count())
                                                            <div class="border rounded overflow-hidden">
                                                                @foreach($indikatorKegiatans as $ind)
                                                                    <div class="d-flex justify-content-between align-items-center px-3 py-1 border-bottom indicator-row">
                                                                        <div>
                                                                            <div class="fw-semibold small text-dark">
                                                                                {{ $ind->tolok_ukur }}
                                                                            </div>
                                                                            <div class="small text-muted">
                                                                                Target:
                                                                                <strong>{{ $ind->target }}</strong>
                                                                                {{ $ind->satuan }}
                                                                            </div>
                                                                        </div>

                                                                        @if(in_array(auth()->user()->role, ['admin','super admin']))
                                                                            <div class="btn-group btn-group-sm">
                                                                                <button class="btn btn-outline-warning btnEditIndikatorKegiatan"
                                                                                    data-id="{{ $ind->id }}"
                                                                                    data-tolok="{{ $ind->tolok_ukur }}"
                                                                                    data-target="{{ $ind->target }}"
                                                                                    data-satuan="{{ $ind->satuan }}">
                                                                                    <i class="fas fa-pencil-alt"></i>
                                                                                </button>

                                                                                <button class="btn btn-outline-danger btnHapusIndikatorKegiatan"
                                                                                    data-id="{{ $ind->id }}">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="alert alert-warning mb-0 py-2 small">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Kegiatan ini belum memiliki indikator output.
                                                            </div>
                                                        @endif

                                                    </div>

                                                </td>
                                            </tr>

                                           {{-- @endif --}}
                                            @php $lastKegiatan = $item->keg_kode; @endphp

                                        @endif
                                        
                                        {{-- BARIS DATA SUB KEGIATAN --}}
                                        <tr>
                                            <!-- [UPDATE] TOMBOL AKSI APPROVAL -->
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <!-- Warna Tombol Berubah Sesuai Status -->
                                                    <button class="btn btn-sm rounded-circle shadow-sm 
                                                        {{ $item->status_validasi == 'valid' ? 'btn-success' : ($item->status_validasi == 'submitted' ? 'btn-warning' : 'btn-primary') }}" 
                                                        type="button" data-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-bars"></i>
                                                    </button>
                                                    
                                                    <ul class="dropdown-menu shadow border-0" style="z-index: 9999;">
                                                        
                                                        {{-- 1. TOMBOL INPUT / EDIT --}}
                                                        <li>
                                                            <a class="dropdown-item py-2" href="{{ route('budget.create', ['id' => $item->pagu_id]) }}">
                                                                @if($item->status_validasi == 'valid' && auth()->user()->role != 'admin')
                                                                    <i class="fas fa-eye text-secondary me-2"></i> Lihat Rincian
                                                                @else
                                                                    <i class="fas fa-pen text-primary me-2"></i> Input / Edit Rincian
                                                                @endif
                                                            </a>
                                                        </li>

                                                        {{-- 2. TOMBOL CETAK (Muncul jika Valid atau Admin) --}}
                                                        @if($item->status_validasi == 'valid' || auth()->user()->role == 'admin')
                                                        <li>
                                                            <a class="dropdown-item py-2 btn-cetak-trigger" 
                                                               href="javascript:void(0)"
                                                               data-url="{{ route('cetak.rka', $item->sub_id ?? $item->pagu_id) }}" 
                                                               data-subnama="{{ $item->sub_nama }}">
                                                                <i class="fas fa-print text-success me-2"></i> Cetak RKA 2.2.1
                                                            </a>
                                                        </li>
                                                        @endif

                                                        <li><hr class="dropdown-divider"></li>

                                                        {{-- 3. LOGIC APPROVAL (USER BIASA) --}}
                                                        @if(auth()->user()->role != 'admin')
                                                            @if($item->status_validasi == 'draft' || $item->status_validasi == 'tolak')
                                                                <li>
                                                                    <form action="{{ route('approval.ajukan', $item->pagu_id) }}" method="POST">
                                                                        @csrf
                                                                        <button class="dropdown-item py-2 text-warning fw-bold" onclick="return confirm('Yakin ajukan data ini? Data akan terkunci.')">
                                                                            <i class="fas fa-paper-plane me-2"></i> Ajukan Validasi
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        @endif

                                                        {{-- 4. LOGIC APPROVAL (ADMIN) --}}
                                                        
                                                           @if(
                                                                in_array(auth()->user()->role, ['admin','super admin'])
                                                                && $item->status_validasi === 'submitted'
                                                            )
                                                                <li>
                                                                    <form action="{{ route('approval.setujui', $item->pagu_id) }}" method="POST">
                                                                        @csrf
                                                                        <button class="dropdown-item py-2 text-success fw-bold">
                                                                            <i class="fas fa-check-circle me-2"></i> Setujui (Validasi)
                                                                        </button>
                                                                    </form>
                                                                </li>

                                                                <li>
                                                                    <button class="dropdown-item py-2 text-danger fw-bold" 
                                                                        onclick="bukaModalTolak('{{ $item->pagu_id }}', '{{ $item->sub_nama }}')">
                                                                        <i class="fas fa-times-circle me-2"></i> Tolak / Revisi
                                                                    </button>
                                                                </li>
                                                            @endif

                                                            {{-- 5. ACTION SETELAH SAH (KHUSUS ADMIN) --}}
                                                            @if(
                                                                in_array(auth()->user()->role, ['admin','super admin'])
                                                                && $item->status_validasi === 'valid'
                                                            )

                                                                <li>
                                                                    <form action="{{ route('approval.batalkanDraft', $item->pagu_id) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="dropdown-item py-2 text-warning fw-bold"
                                                                            onclick="return confirm('Kembalikan ke Draft? Status SAH akan dibatalkan.')">
                                                                            <i class="fas fa-undo me-2"></i> Kembalikan ke Draft
                                                                        </button>
                                                                    </form>
                                                                </li>

                                                                <li>
                                                                    <button type="button"
                                                                        class="dropdown-item py-2 text-danger fw-bold"
                                                                        onclick="bukaModalRevisiAdmin('{{ $item->pagu_id }}', '{{ $item->sub_nama }}')">
                                                                        <i class="fas fa-edit me-2"></i> Revisi Admin
                                                                    </button>
                                                                </li>

                                                            @endif

                                                    </ul>
                                                </div>
                                            </td>

                                            <!-- Nama Sub Kegiatan -->
                                            <td class="ps-5">

                                                @if($item->kegiatan_has_indikator)

                                                    <a href="{{ route('budget.create', ['id' => $item->pagu_id]) }}"
                                                    class="text-decoration-none fw-bold text-primary">
                                                        {{ $item->sub_kode }} {{ $item->sub_nama }}
                                                    </a>

                                                @else

                                                    <span class="text-danger fw-bold">
                                                        {{ $item->sub_kode }} {{ $item->sub_nama }}
                                                        <br>
                                                        <small>
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Isi indikator kegiatan dulu
                                                        </small>
                                                    </span>

                                                @endif

                                            </td>

                                            <!-- [UPDATE] KOLOM STATUS (BADGE) -->
                                            <td class="text-center">
                                                @if($item->status_validasi == 'draft')
                                                    <span class="badge bg-secondary">DRAFT</span>
                                                @elseif($item->status_validasi == 'submitted')
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> VERIFIKASI</span>
                                                @elseif($item->status_validasi == 'valid')
                                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> SAH</span>
                                                @elseif($item->status_validasi == 'tolak')
                                                    <span class="badge bg-danger mb-1">REVISI</span>
                                                    <br>
                                                    <!-- TOMBOL LIHAT CATATAN -->
                                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" 
                                                        style="font-size: 0.7rem;"
                                                        onclick="lihatCatatan('{{ $item->catatan_revisi }}')">
                                                        <i class="fas fa-comment-alt me-1"></i> Lihat Pesan
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary">DRAFT</span>
                                                @endif
                                            </td>

                                            <!-- Pagu -->
                                            <td class="text-end font-monospace text-secondary">
                                                {{ number_format($item->pagu_nilai, 0, ',', '.') }}
                                            </td>

                                            <!-- Realisasi -->
                                            <td class="text-end font-monospace fw-bold text-dark">
                                                {{ number_format($item->terpakai, 0, ',', '.') }}
                                            </td>

                                            <!-- Persen -->
                                            <td class="text-end fw-bold {{ $item->persen >= 100 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($item->persen, 2) }} %
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <!-- State Kosong -->
                <div class="card shadow-sm border-0 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-arrow-left fa-3x text-primary mb-3"></i>
                        <h4 class="text-muted">Pilih Unit Kerja</h4>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL CETAK (SOLUSI 3) -->
<!-- ======================================================= -->
<div class="modal fade" id="modalCetak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="fas fa-print me-2"></i>Cetak Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="" method="GET" id="formCetak" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        Anda akan mencetak: <br>
                        <b id="label_sub_kegiatan">...</b>
                    </div>

                    <!-- 1. Pilihan Tanggal -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tempat</label>
                            <input type="text" name="tempat_ttd" class="form-control" placeholder="Default: Sesuai Instansi">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tanggal</label>
                            <input type="date" name="tanggal_ttd" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <!-- 2. Pilihan Pejabat (Dropdown) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Penandatangan (PA/KPA)</label>
                        <select name="pejabat_id" class="form-select">
                            @if(isset($pejabats))
                                @foreach($pejabats as $p)
                                    <option value="{{ $p->id }}" {{ Str::contains($p->jabatan, 'Kepala') ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->jabatan }})
                                    </option>
                                @endforeach
                            @else
                                <option value="">Data Pejabat Tidak Ditemukan</option>
                            @endif
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" onclick="$('#modalCetak').modal('hide')">
                        <i class="fas fa-file-pdf me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Indikator Program -->
<div class="modal fade" id="modalIndikatorProgram" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title">Tambah Indikator Program</h6>
                <button type="button" class="close close-white" data-dismiss="modal"></button>
            </div>

            <form id="formIndikatorProgram">
                @csrf
                <input type="hidden" name="m_program_id" id="inputProgramId">
                <input type="hidden" name="jenis" value="outcome">  <!-- WAJIB ADA -->

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tolok Ukur</label>
                        <textarea name="tolok_ukur" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target</label>
                        <input type="number" step="0.01" name="target" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Satuan</label>
                        <input type="text" name="satuan" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Modal Indikator Kegiatan-->
<div class="modal fade" id="modalIndikatorKegiatan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-secondary text-white">
                <h6 class="modal-title">Tambah Indikator Kegiatan (Output)</h6>
                <button type="button" class="close close-white" data-dismiss="modal"></button>
            </div>

            <form id="formIndikatorKegiatan">
                @csrf

                <input type="hidden" name="m_program_id" id="inputKegiatanId">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tolok Ukur</label>
                        <textarea name="tolok_ukur" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target</label>
                        <input type="number" step="0.01" name="target" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Satuan</label>
                        <input type="text" name="satuan" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn light" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL TOLAK / REVISI (KHUSUS ADMIN) -->
<!-- ======================================================= -->
@if(auth()->user()->role == 'admin')
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Tolak & Minta Revisi</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form id="formTolak" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Anda akan mengembalikan RKA: <br><b id="label_tolak_nama">...</b></p>
                    <div class="mb-3">
                        <label class="fw-bold">Alasan Penolakan / Catatan:</label>
                        <textarea name="catatan" class="form-control" rows="3" required placeholder="Contoh: Harga satuan terlalu mahal, tolong cek SSH..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
$(document).ready(function(){

    console.log("RKA SCRIPT READY");

    // =====================================================
    // 1️⃣ MODAL CETAK
    // =====================================================
    $('.btn-cetak-trigger').on('click', function(e){
        e.preventDefault();

        var url  = $(this).data('url');
        var nama = $(this).data('subnama');

        $('#formCetak').attr('action', url);
        $('#label_sub_kegiatan').text(nama);

        $('#modalCetak').modal('show');
    });


    // =====================================================
    // 2️⃣ MODAL TOLAK (ADMIN)
    // =====================================================
    window.bukaModalTolak = function(id, nama){

        var url = "{{ route('approval.tolak', ':id') }}";
        url = url.replace(':id', id);

        $('#formTolak').attr('action', url);
        $('#label_tolak_nama').text(nama);

        $('#modalTolak').modal('show');
    };


    // =====================================================
    // 3️⃣ MODAL LIHAT CATATAN
    // =====================================================
    window.lihatCatatan = function(pesan){

        if(!pesan || pesan === 'null'){
            pesan = "Tidak ada catatan spesifik. Silakan cek kelengkapan data.";
        }

        $('#isi_catatan_revisi').text(pesan);
        $('#modalLihatCatatan').modal('show');
    };


    // =====================================================
    // 4️⃣ INDIKATOR PROGRAM
    // =====================================================

    // Set ID saat modal dibuka
    $('#modalIndikatorProgram').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var programId = button.data('program-id');

        $('#formIndikatorProgram')[0].reset();
        $('#formIndikatorProgram').removeAttr('data-edit-id');

        $('#inputProgramId').val(programId);

        console.log("Program ID:", programId);
    });

    // Submit indikator program
    $('#formIndikatorProgram').on('submit', function(e){

        e.preventDefault();

        console.log("Submit indikator program");

        var form = $(this);
        var editId = form.attr('data-edit-id');

        var url = editId
            ? '/indikator-program/' + editId
            : "{{ route('indikator.program.store') }}";

        var formData = new FormData(this);

        if(editId){
            formData.append('_method','PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                console.log(res);
                location.reload();
            },
            error: function(xhr){
                console.log(xhr.responseText);
                alert('Terjadi kesalahan');
            }
        });
    });

    // Hapus indikator program
    $('.btnHapusIndikator').on('click', function(){

        var id = $(this).data('id');

        if(confirm('Hapus indikator ini?')){

            $.ajax({
                url: '/indikator-program/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(){
                    location.reload();
                }
            });
        }
    });



    // =====================================================
    // 5️⃣ INDIKATOR KEGIATAN
    // =====================================================

    $('#modalIndikatorKegiatan').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var kegiatanId = button.data('kegiatan-id');

        $('#formIndikatorKegiatan')[0].reset();
        $('#formIndikatorKegiatan').removeAttr('data-edit-id');

        $('#inputKegiatanId').val(kegiatanId);

        console.log("Kegiatan ID:", kegiatanId);
    });

    $('#formIndikatorKegiatan').on('submit', function(e){

        e.preventDefault();

        console.log("Submit indikator kegiatan");

        var form = $(this);
        var editId = form.attr('data-edit-id');

        var url = editId
            ? '/indikator-program/' + editId
            : "{{ route('indikator.program.store') }}";

        var formData = new FormData(this);
        formData.append('jenis','output');

        if(editId){
            formData.append('_method','PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                location.reload();
            },
            error: function(xhr){
                console.log(xhr.responseText);
                alert('Terjadi kesalahan');
            }
        });
    });

});
</script>
@endpush

@endsection

<style>
.indicator-row {
    border-left: 4px solid #0d6efd;
    background-color: #f8fbff;
}

.indicator-row:last-child {
    border-bottom: none !important;
}
</style>

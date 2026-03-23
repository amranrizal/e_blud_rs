@extends('layouts.app')

{{-- Tambahkan CSS Select2 --}}
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Target Pendapatan</h1>
        <a href="{{ route('pendapatan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Input Target RBA</h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('pendapatan.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        
                        <!-- Tahun Anggaran -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun Anggaran</label>
                            <input type="number" name="tahun" class="form-control bg-light" value="{{ date('Y') }}" readonly>
                        </div>

                        <!-- Unit Kerja (SEARCHABLE) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Kerja</label>
                            <select name="unit_id" class="form-select select2">
                                <option value="">-- Pilih Unit (Opsional) --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.75rem;">*Pilih jika pendapatan spesifik per unit</small>
                        </div>

                        <!-- Pilih Akun Pendapatan (SEARCHABLE) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode Rekening Pendapatan <span class="text-danger">*</span></label>
                            <select name="kode_akun" class="form-select select2" required>
                                <option value="">-- Ketik Nama / Kode Akun --</option>
                                @foreach($rekenings as $rek)
                                    <option value="{{ $rek->kode_akun }}">
                                        {{ $rek->kode_akun }} - {{ $rek->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @if($rekenings->isEmpty())
                                <small class="text-danger mt-1 d-block">
                                    <i class="fas fa-exclamation-circle"></i> Data Akun Pendapatan (4.x.x) belum ada di Master Rekening.
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        
                        <!-- Uraian -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Uraian Detail <span class="text-danger">*</span></label>
                            <textarea name="uraian" class="form-control" rows="2" placeholder="Contoh: Target Retribusi Parkir Umum" required></textarea>
                        </div>

                        <!-- Kalkulator Target -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="small fw-bold text-secondary">Volume</label>
                                        <input type="number" name="volume" id="volume" class="form-control form-control-sm" placeholder="0" required oninput="hitungTotal()">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small fw-bold text-secondary">Satuan</label>
                                        <input type="text" name="satuan" class="form-control form-control-sm" placeholder="Cth: Pasien">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small fw-bold text-secondary">Tarif / Harga</label>
                                        <input type="number" name="tarif" id="tarif" class="form-control form-control-sm" placeholder="0" required oninput="hitungTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Readonly -->
                        <div class="mb-3 text-end">
                            <label class="form-label fw-bold text-secondary small">TOTAL TARGET (Rp)</label>
                            <input type="text" id="total_display" class="form-control form-control-lg font-weight-bold text-success text-end border-0 bg-transparent" value="0" readonly>
                        </div>

                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('pendapatan.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Target
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<!-- Script Hitung Otomatis & Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // 1. Aktivasi Select2 (Pencarian)
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).data('placeholder'),
        });
    });

    // 2. Hitung Perkalian Volume x Tarif
    function hitungTotal() {
        let vol = parseFloat(document.getElementById('volume').value) || 0;
        let tarif = parseFloat(document.getElementById('tarif').value) || 0;
        let total = vol * tarif;

        // Format Rupiah Indonesia
        document.getElementById('total_display').value = new Intl.NumberFormat('id-ID').format(total);
    }
</script>
@endsection
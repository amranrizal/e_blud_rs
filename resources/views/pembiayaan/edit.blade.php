@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 40px !important; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
    
    /* Style Readonly */
    input[readonly] {
        background-color: #eaecf4 !important;
        cursor: not-allowed;
    }
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Pembiayaan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('pembiayaan.update', $pembiayaan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Kode Rekening Pembiayaan <span class="text-danger">*</span></label>
                <select name="kode_akun" id="kode_rekening" class="form-control" required>
                    <option value="">-- Cari Kode / Nama Akun --</option>
                    @foreach($akuns as $akun)
                        <option value="{{ $akun->kode_akun }}" 
                            data-nama="{{ $akun->nama_akun }}"
                            {{ $akun->kode_akun == $pembiayaan->kode_akun ? 'selected' : '' }}>
                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Uraian (Otomatis)</label>
                {{-- READONLY & Value diambil dari DB --}}
                <input type="text" name="uraian" id="uraian" class="form-control" value="{{ $pembiayaan->uraian }}" readonly>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>Volume</label>
                    <input type="number" name="volume" id="volume" class="form-control" value="{{ $pembiayaan->volume + 0 }}" step="any" oninput="hitung()" required>
                </div>
                <div class="col-md-4">
                    <label>Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="{{ $pembiayaan->satuan }}">
                </div>
                <div class="col-md-4">
                    <label>Harga Satuan (Rp)</label>
                    <input type="number" name="harga_satuan" id="harga" class="form-control" value="{{ $pembiayaan->harga_satuan }}" oninput="hitung()" required>
                </div>
            </div>
            
            <div class="mt-3">
                <label>Total Preview</label>
                <input type="text" id="total_view" class="form-control" readonly style="background:#eee; font-weight:bold;">
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> Update Perubahan
                </button>
                <a href="{{ route('pembiayaan.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#kode_rekening').select2({ width: '100%' });
        
        // Hitung total saat load pertama kali
        hitung(); 

        // Update Uraian saat dropdown berubah
        $('#kode_rekening').on('change', function() {
            var selectedOption = $(this).find(':selected');
            var namaAkun = selectedOption.data('nama');
            
            if(namaAkun) {
                $('#uraian').val(namaAkun);
            }
        });
    });

    function hitung() {
        let v = $('#volume').val() || 0;
        let h = $('#harga').val() || 0;
        let t = v * h;
        $('#total_view').val(new Intl.NumberFormat('id-ID').format(t));
    }
</script>
@endsection
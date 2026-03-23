@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 40px !important; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
    
    /* Style untuk input readonly agar terlihat seperti disabled (abu-abu) */
    input[readonly] {
        background-color: #eaecf4 !important; 
        cursor: not-allowed;
    }
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Input Pembiayaan (Akun 6)</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('pembiayaan.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Kode Rekening Pembiayaan <span class="text-danger">*</span></label>
                <select name="kode_akun" id="kode_rekening" class="form-control" required>
                    <option value="">-- Cari Kode / Nama Akun --</option>
                    @foreach($akuns as $akun)
                        {{-- SAYA TAMBAHKAN data-nama DISINI --}}
                        <option value="{{ $akun->kode_akun }}" data-nama="{{ $akun->nama_akun }}">
                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Uraian (Otomatis)</label>
                {{-- Input ini akan otomatis terisi dan READONLY --}}
                <input type="text" name="uraian" id="uraian" class="form-control" readonly placeholder="Pilih rekening diatas dulu...">
            </div>

            <div class="row">
                <div class="col-md-4">
                    <label>Volume</label>
                    <input type="number" name="volume" id="volume" class="form-control" value="1" step="any" oninput="hitung()" required>
                </div>
                <div class="col-md-4">
                    <label>Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="Tahun">
                </div>
                <div class="col-md-4">
                    <label>Harga Satuan (Rp)</label>
                    <input type="number" name="harga_satuan" id="harga" class="form-control" oninput="hitung()" required>
                </div>
            </div>
            
            <div class="mt-3">
                <label>Total Preview</label>
                <input type="text" id="total_view" class="form-control" readonly style="background:#eee; font-weight:bold;">
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Data
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
        // Init Select2
        $('#kode_rekening').select2({
            placeholder: "-- Ketik untuk mencari --",
            allowClear: true,
            width: '100%'
        });

        // EVENT LISTENER SAAT MEMILIH REKENING
        $('#kode_rekening').on('change', function() {
            // Ambil element option yang dipilih
            var selectedOption = $(this).find(':selected');
            
            // Ambil data-nama dari option tersebut
            var namaAkun = selectedOption.data('nama');
            
            // Masukkan ke kolom Uraian
            if(namaAkun) {
                $('#uraian').val(namaAkun);
            } else {
                $('#uraian').val(''); // Kosongkan jika batal pilih
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
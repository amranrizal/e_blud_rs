@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Judul -->
        <h4 class="fw-bold m-0 text-primary">Master Rekening (Chart of Accounts)</h4>
        
        <!-- Tombol Group (Kanan) -->
        @if(Auth::user()->role == 'admin')
            <div>
                <!-- Tombol Import -->
                <button class="btn btn-sm btn-success mr-1 shadow-sm" type="button" data-toggle="modal" data-target="#modalImportRekening">
                    <i class="fas fa-file-excel"></i> Import Excel
                </button>
                
                <!-- Tombol ini memicu Modal ID #modalRekening -->
                <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalRekening">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Akun Utama (Level 1)
                </button>
            </div>
        @endif
    </div>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <label class="fw-bold mb-2">🔎 Lompat Cepat ke Rekening</label>
            <select id="jumpRekening" class="form-control"></select>
        </div>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="15%">Kode Rekening</th>
                        <th>Uraian Akun</th>
                        <th width="10%">Level</th>
                        {{-- Header Kolom Aksi (Hanya Admin) --}}
                        @if(auth()->user()->role === 'admin')
                            <th class="text-center" width="15%">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @forelse($akuns as $akun)
                <tr data-id="{{ $akun->id }}">
                    <td class="text-start">
                        {{ $akun->kode_akun }}
                    </td>

                    <td style="padding-left: {{ substr_count($akun->kode_akun, '.') * 20 }}px;">
                        {{ $akun->nama_akun }}
                    </td>

                    <td>                       
                            {{ $akun->level }}                   
                    </td>

                    @if(auth()->user()->role === 'admin')
                    <td class="text-center">
                        @include('master.rekening.partials.action-buttons', ['row' => $akun])
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? 4 : 3 }}" class="text-center">
                        Belum ada data rekening.
                    </td>
                </tr>
                @endforelse
                </tbody>

            </table>
            <div class="mt-3">
                {{ $akuns->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPORT REKENING -->
<div class="modal fade" id="modalImportRekening" tabindex="-1" role="dialog" aria-labelledby="modalImportRekeningLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        
        <form action="{{ route('master.rekening.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalImportRekeningLabel">
                    <i class="fas fa-file-excel mr-2"></i> Import Chart of Accounts
                </h5>
                <!-- Tombol Close (Support Bootstrap 4 & 5) -->
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <!-- Panduan Format Excel -->
                <div class="alert alert-info border-left-info shadow-sm">
                    <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Aturan File Excel:</h6>
                    <small>
                        1. Header Kolom A wajib: <code>kode_akun</code><br>
                        2. Header Kolom B wajib: <code>nama_akun</code><br>
                        3. Data harus <strong>URUT</strong> dari atas ke bawah (Induk dulu, baru Anak).<br>
                        4. Sistem otomatis mengisi <em>Level</em> & <em>Parent ID</em>.
                    </small>
                </div>

                <!-- Input File -->
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Pilih File Excel (.xlsx / .xls)</label>
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="form-control" id="fileExcelRekening" required accept=".xlsx, .xls">
                    </div>
                    <small class="text-muted mt-1 d-block">Maksimal ukuran file: 2MB</small>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-upload"></i> Upload & Import
                </button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL KHUSUS LEVEL 1 -->
<div class="modal fade" id="modalRekening" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Tambah Akun Utama (Level 1)</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('master.rekening.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2" style="font-size: 0.9em;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Gunakan tombol ini hanya untuk membuat <strong>Akun Induk (Cth: 4, 5, 6)</strong>.
                        Untuk sub-rincian, gunakan tombol <strong>(+) Hijau</strong> di tabel.
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Kode Akun</label>
                        <input type="text" name="kode_akun" class="form-control" placeholder="Contoh: 5" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Uraian Akun</label>
                        <input type="text" name="nama_akun" class="form-control" placeholder="Contoh: BELANJA" required>
                    </div>

                    <input type="hidden" name="level" value="Akun">
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
$(function() {

    // ===============================
    // INIT SELECT2
    // ===============================
    $('#jumpRekening').select2({
        width: '100%',
        placeholder: 'Ketik kode atau nama rekening...',
        minimumInputLength: 1,
        ajax: {
            url: "{{ route('master.rekening.search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                console.log("SEARCH TERM:", params.term);
                return { q: params.term };
            },
            processResults: function (data) {
                console.log("HASIL DARI SERVER:", data);
                return { results: data };
            },
            error: function (xhr) {
                console.log("ERROR AJAX:", xhr.responseText);
            }
        }
    });

    // ===============================
    // REDIRECT SAAT DIPILIH
    // ===============================
    $('#jumpRekening').on('select2:select', function (e) {
        let id = e.params.data.id;
        window.location.href = "{{ route('master.rekening.goto') }}" + "?id=" + id;
    });

    // ===============================
    // 🔥 HIGHLIGHT SETELAH REDIRECT
    // ===============================
    const params = new URLSearchParams(window.location.search);
    const selectedId = params.get('selected_id');

    if (selectedId) {

        const row = $('tr[data-id="' + selectedId + '"]');

        if (row.length) {

            row.addClass('table-active');

            const y = row[0].getBoundingClientRect().top + window.pageYOffset - 150;

            window.scrollTo({
                top: y,
                behavior: "smooth"
            });

        } else {
            console.log('Row tidak ditemukan di halaman ini');
        }
    }

});
</script>
@endpush

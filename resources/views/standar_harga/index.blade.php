@extends('layouts.app')

@section('content')

<style>
.bg-purple {
    background:#6f42c1;
}
</style>

<div class="container-fluid">

    <!-- ========================= -->
    <!-- HEADER -->
    <!-- ========================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark fw-bold mb-0">💰 Master Standar Harga (SSH / SBU / HSPK)</h3>
        <div class="d-flex gap-2">
            <button type="button"
                class="btn btn-primary btn-sm shadow-sm"
                data-toggle="modal"
                data-target="#modalTambahSsh">
                <i class="fas fa-plus-circle me-1"></i> Tambah SSH
            </button>

            <button type="button"
                class="btn btn-success btn-sm shadow-sm"
                data-toggle="modal"
                data-target="#modalImport">
                <i class="fas fa-file-excel me-1"></i> Import Excel
            </button>
        </div>
    </div>

    <!-- ========================= -->
    <!-- CARD TABLE -->
    <!-- ========================= -->
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <!-- FILTER -->
            <form action="{{ route('standar-harga.index') }}" method="GET" class="mb-3">
                <div class="d-flex gap-2">
                    <select name="tahun"
                            class="form-select"
                            style="width: 120px;"
                            onchange="this.form.submit()">
                        @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                Tahun {{ $i }}
                            </option>
                        @endfor
                    </select>

                    <div class="input-group" style="max-width: 300px;">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Cari barang..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="d-flex justify-content-between align-items-center mb-3">

            <form method="GET">

            <div class="d-flex align-items-center">

            <span class="me-2">Items per page:</span>

            <select name="perPage"
                    class="form-select form-select-sm"
                    style="width:90px"
                    onchange="this.form.submit()">

            <option value="10" {{ request('perPage') == 10 ? 'selected':'' }}>10</option>
            <option value="25" {{ request('perPage') == 25 ? 'selected':'' }}>25</option>
            <option value="50" {{ request('perPage') == 50 ? 'selected':'' }}>50</option>
            <option value="100" {{ request('perPage') == 100 ? 'selected':'' }}>100</option>

            </select>

            </div>

            </form>

            </div>
            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th width="160">Kode</th>
                        <th width="90">Jenis</th>
                        <th>Uraian</th>
                        <th>Spesifikasi</th>
                        <th width="90">Satuan</th>
                        <th class="text-end" width="120">Harga</th>
                        <th width="200">Kode Rekening</th>
                        <th class="text-center" width="110">Aksi</th>
                    </tr>
                    </thead>

                    <tbody>

                    @forelse($datas as $item)

                        <tr style="{{ is_null($item->parent_id) ? 'background:#eef2ff;font-weight:600;' : '' }}">

                            <!-- KODE -->
                            <td class="font-monospace {{ is_null($item->parent_id) ? '' : 'ps-4' }}">
                                {{ $item->kode_barang }}
                            </td>

                            <!-- JENIS -->
                            <td>
                                @if(is_null($item->parent_id))
                                    <span class="badge bg-dark">GROUP</span>
                                @else
                                    <span class="badge bg-primary">SSH</span>
                                @endif
                            </td>

                            <!-- URAIAN -->
                            <td class="{{ is_null($item->parent_id) ? '' : 'ps-4' }}">
                                {{ $item->uraian }}
                            </td>

                            <td>
                                @if(!is_null($item->parent_id))
                                    {{ $item->spesifikasi ?? '-' }}
                                @endif
                            </td>

                            <td>
                                @if(!is_null($item->parent_id))
                                    {{ $item->satuan }}
                                @endif
                            </td>

                            <td class="text-end fw-bold">
                                @if(!is_null($item->parent_id) && $item->harga)
                                    Rp {{ number_format($item->harga,0,',','.') }}
                                @endif
                            </td>

                            <td class="kode-rekening">
                                @if($item->kode_rekening)
                                    @foreach(explode(',', $item->kode_rekening) as $kode)
                                        <div>{{ trim($kode) }}</div>
                                    @endforeach
                                @endif
                            </td>

                            <td class="text-center">
                                <td class="text-center">

                                    @if(!is_null($item->parent_id))

                                        <!-- EDIT -->
                                        <button 
                                            class="btn btn-sm btn-warning btn-edit-ssh"
                                            data-id="{{ $item->id }}"
                                            data-kelompok="{{ $item->kode_kelompok }}"
                                            data-uraian="{{ $item->uraian }}"
                                            data-spek="{{ $item->spesifikasi }}"
                                            data-satuan="{{ $item->satuan }}"
                                            data-harga="{{ $item->harga }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- DELETE -->
                                        <form action="{{ route('standar-harga.destroy', $item->id) }}" 
                                            method="POST" 
                                            class="d-inline form-delete">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    @endif

                                </td>
                            </td>

                        </tr>

                        @empty  

                    <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                    Belum ada data SSH
                    </td>
                    </tr>

                    @endforelse

                    </tbody>

                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">

                <div>

                Showing
                {{ $datas->firstItem() }}
                -
                {{ $datas->lastItem() }}
                of
                {{ $datas->total() }}

                </div>

                <div>

                {{ $datas->links() }}

                </div>

                </div>
            </div>

            <div class="mt-3">
              
            </div>

        </div>
    </div>
</div>

<!-- ========================== -->
<!-- MODAL 1: IMPORT EXCEL -->
<!-- ========================== -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('standar-harga.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data SSH/SBU</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number"
                            name="tahun"
                            class="form-control"
                            value="{{ date('Y') }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel (.xlsx)</label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text small text-muted">Pastikan header kolom sesuai format.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload & Proses</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================== -->
<!-- MODAL 2: EDIT DATA -->
<!-- ========================== -->
<div class="modal fade" id="modalEditSsh" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Edit SSH</h5>
                <button type="button" class="btn-close" data-dismiss="modal"></button>
            </div>
            
            <form id="formEditSsh" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Baris 1 -->
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Kelompok</label>
                            <select name="kode_kelompok" id="edit_kelompok" class="form-select">
                                <option value="SSH">SSH</option>
                                <option value="SBU">SBU</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Uraian / Nama Barang</label>
                            <input type="text" name="uraian" id="edit_uraian" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Spesifikasi</label>
                            <textarea name="spesifikasi" id="edit_spek" class="form-control" rows="2"></textarea>
                        </div>

                        <!-- Baris 3 -->
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga" id="edit_harga" class="form-control fw-bold text-primary" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================== -->
<!-- MODAL TAMBAH SSH -->
<!-- ========================== -->
<div class="modal fade" id="modalTambahSsh" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle"></i> Tambah SSH
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-dismiss="modal"></button>
            </div>

            <form action="{{ route('standar-harga.store') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" id="parent_id">
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- KODE BARANG -->
                        <div class="alert alert-info">
                            Kode barang akan dibuat otomatis berdasarkan group yang dipilih
                        </div>

                        <!-- KELOMPOK -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Kelompok *</label>
                            <select name="kode_kelompok" class="form-select" required>
                                <option value="SSH">SSH</option>
                                <option value="SBU">SBU</option>
                            </select>
                        </div>

                        <!-- KODE REKENING -->
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Kode Rekening</label>

                            <select name="kode_rekening[]" 
                                    class="form-select select-rekening"
                                    multiple>
                            </select>
                        </div>

                         <!-- DropDown Group -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Group *</label>
                            <select name="parent_id" id="parent_id" class="form-select select-group" required>                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" data-uraian="{{ $g->uraian }}">
                                        {{ $g->kode_barang }} - {{ $g->uraian }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- URAIAN -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Uraian / Nama Barang *
                            </label>
                            <input type="text" name="uraian" id="uraian" class="form-control" readonly style="background:#e9ecef;">
                        </div>

                        <!-- SPESIFIKASI -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Spesifikasi</label>
                            <textarea name="spesifikasi"
                                    class="form-control"
                                    rows="2"></textarea>
                        </div>

                        <!-- SATUAN -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Satuan *</label>
                            <input type="text"
                                name="satuan"
                                class="form-control"
                                required>
                        </div>

                        <!-- HARGA -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Harga *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                    name="harga"
                                    class="form-control fw-bold"
                                    required>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">Batal</button>

                    <button type="submit"
                            class="btn btn-primary fw-bold">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

    /* =========================
       TOAST GLOBAL
    ========================= */
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}" });
    @endif

    @if(session('error'))
        Toast.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}" });
    @endif

    @if(session('error_kode_akun'))
        Toast.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            text: "{{ session('error_kode_akun') }}"
        });
    @endif


    /* =========================
    TOMBOL EDIT SSH
    ========================= */
    $(document).on('click', '.btn-edit-ssh', function () {

        let id     = $(this).data('id');
        let kel    = $(this).data('kelompok');
        let uraian = $(this).data('uraian');
        let spek   = $(this).data('spek');
        let satuan = $(this).data('satuan');
        let harga  = $(this).data('harga');
        
        // isi field modal
        $('#edit_kelompok').val(kel);
        $('#edit_uraian').val(uraian);
        $('#edit_spek').val(spek);
        $('#edit_satuan').val(satuan);
        $('#edit_harga').val(harga);

        // set action form
        let url = "{{ route('standar-harga.update', ':id') }}"
            .replace(':id', id);

        $('#formEditSsh').attr('action', url);

        $('#modalEditSsh').modal('show');
    });

    /* =========================
       AUTO OPEN MODAL EDIT
    ========================= */
    @if(session('open_modal_edit'))
        setTimeout(function () {
            $('.btn-edit-ssh[data-id="{{ session('open_modal_edit') }}"]').trigger('click');
        }, 300);
    @endif

    @if(session('open_modal_tambah'))
        $('#modalTambahSsh').modal('show');
    @endif

});

$(document).on('shown.bs.modal', '#modalTambahSsh', function () {

    // ===== REKENING =====
    let el = $(this).find('.select-rekening');

    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.empty();

    el.select2({
        placeholder: "Ketik kode / nama rekening...",
        width: '100%',
        dropdownParent: $('#modalTambahSsh'),
        minimumInputLength: 0,
        ajax: {
            url: '/master/rekening/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term || '' };
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        }
    });


    // ===== GROUP (🔥 INI YANG KURANG) =====
    let groupEl = $(this).find('.select-group');

    if (groupEl.hasClass("select2-hidden-accessible")) {
        groupEl.select2('destroy');
    }

    // 🔥 RESET VALUE (INI YANG FIX "NEMPEL")
    groupEl.val(null).trigger('change');

    groupEl.select2({
        placeholder: "-- Pilih Group --",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahSsh')
    });

});

// saat pilih group
$(document).on('change', '#parent_id', function () {

    let groupText = $(this).find(':selected').data('uraian');
    let spesifikasi = $('#spesifikasi').val();

    if (groupText) {
        $('#uraian').val(
            spesifikasi 
            ? groupText + ' - ' + spesifikasi 
            : groupText + ' - '
        );
    }

});

// saat isi spesifikasi
$(document).on('input', '#spesifikasi', function () {

    let spesifikasi = $(this).val();
    let groupText = $('#parent_id').find(':selected').data('uraian');

    if (groupText) {
        $('#uraian').val(groupText + ' - ' + spesifikasi);
    }

});

$(document).on('submit', '.form-delete', function(e) {
    e.preventDefault();

    let form = this;

    Swal.fire({
        title: 'Yakin hapus?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
@endpush



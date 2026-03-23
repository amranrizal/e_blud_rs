@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
   <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Kiri: JUDUL -->
        <h3 class="fw-bold m-0">🏛️ Master Nomenklatur (SIPD)</h3>
        
        <!-- Kanan: TOMBOL (Dibungkus Div agar Nempel) -->
        @if(Auth::user()->role == 'admin')
            <div>
                <!-- Tombol Import (Jarak tipis pakai mr-1) -->
                <button class="btn btn-sm btn-success mr-1 shadow-sm" type="button" data-toggle="modal" data-target="#modalImport">
                    <i class="fas fa-file-excel"></i> Import Excel
                </button>
                
                <!-- Tombol Tambah -->
                <button class="btn btn-sm btn-dark shadow-sm" onclick="tambahData('Urusan', null, null)">
                    <i class="fas fa-plus"></i> Tambah Urusan
                </button>
            </div>
        @endif
    </div>
    <div>
        Per page: {{ $perPage }} |
        Halaman: {{ $programs->currentPage() }} |
        Root count halaman ini: {{ $programs->count() }}
    </div>

    Jumlah data: {{ $programs->total() }}
    Halaman sekarang: {{ $programs->currentPage() }}

    <div class="mb-3">
        <select id="searchProgram" class="form-control" style="width:100%"></select>
    </div>

<form method="GET" class="mb-3">
    <label>Tampilkan:</label>
    <select name="per_page" onchange="this.form.submit()" class="form-select w-auto d-inline">
        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
    </select>
</form>

    <div class="card">
    <div class="card-body p-0">

            <table id="table-program" class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Uraian</th>
                    <th>Level</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                function nextLevel($level) {
                    $map = [
                        'Urusan' => 'Bidang Urusan',
                        'Bidang Urusan' => 'Program',
                        'Program' => 'Kegiatan',
                        'Kegiatan' => 'Sub Kegiatan'
                    ];
                    return $map[$level] ?? null;
                }
                @endphp
                @foreach($programs as $item)
                <tr data-id="{{ $item->id }}">
                    <td>{{ $item->kode_program }}</td>

                    <td>
                        @php
                        $indentMap = [
                            'Urusan' => 0,
                            'Bidang Urusan' => 1,
                            'Program' => 2,
                            'Kegiatan' => 3,
                            'Sub Kegiatan' => 4,
                        ];
                        @endphp

                        {!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $indentMap[$item->level] ?? 0) !!}
                        {{ $item->nama_program }}
                    </td>

                    <td>{{ $item->level }}</td>

                    <td class="text-center">
                        <div class="aksi-wrapper">

                            {{-- Tambah Child --}}
                            @if($item->level !== 'Sub Kegiatan')
                                <button type="button"
                                        class="btn btn-success btn-sm aksi-btn"
                                        title="Tambah Child"
                                        onclick="tambahData('{{ $item->level }}', '{{ $item->id }}', '{{ $item->kode_program }} - {{ $item->nama_program }}')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            @endif

                            {{-- Edit --}}
                            <button type="button"
                                    class="btn btn-warning btn-sm aksi-btn"
                                    title="Edit"
                                    onclick="editForm('{{ $item->id }}')">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Hapus --}}
                            <form action="{{ route('master.program.destroy', $item->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger btn-sm aksi-btn"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $programs->links() }}
        </div>
    </div>
</div>


<!-- MODAL INPUT -->
<div class="modal fade" id="modalInput" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <!-- FIX WIDTH: Memaksa lebar 500px dan tengah -->
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px !important; margin: 1.75rem auto !important;">
        
        <form action="{{ route('master.program.store') }}" method="POST" class="modal-content" id="formProgram">
            @csrf
            
            <div id="methodField"></div>
            <input type="hidden" name="level" id="inputLevel">
            <input type="hidden" name="parent_id" id="inputParentId">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <span id="titleAction">Tambah</span> <span id="labelLevel"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalInput').modal('hide')"></button>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-warning border border-warning" id="boxInduk" style="display:none">
                    <small>Induk:</small><br>
                    <strong id="labelInduk"></strong>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Kode</label>
                    <input type="text" name="kode_program" id="kode_program" class="form-control" required autocomplete="off">
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Uraian / Nama</label>
                    <textarea name="nama_program" id="nama_program" class="form-control" rows="3" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modalInput').modal('hide')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('master.program.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalImportLabel">Import Data Nomenklatur</h5>
                <!-- Tombol Close Support BS4 & BS5 -->
                <button type="button" class="close" data-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-info">
                    <small>
                        <strong>Aturan File Excel:</strong><br>
                        1. Kolom A: <code>kode_program</code> (Contoh: 1, 1.01, 1.01.01)<br>
                        2. Kolom B: <code>nama_program</code><br>
                        3. <strong>WAJIB URUT:</strong> Dari Induk ke Anak (A-Z).
                    </small>
                </div>
                <div class="form-group mb-3">
                    <label>Pilih File Excel (.xlsx)</label>
                    <input type="file" name="file_excel" class="form-control" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Upload & Import</button>
            </div>
        </form>
    </div>
</div>

<script>
    // FUNGSI TAMBAH
    function tambahData(level, parentId, parentCode) {
        $('#formProgram')[0].reset();
        $('#methodField').empty(); 
        $('#formProgram').attr('action', "{{ route('master.program.store') }}");

        $('#titleAction').text('Tambah');
        $('#labelLevel').text(level);
        $('#inputLevel').val(level);
        $('#inputParentId').val(parentId);

        if(parentCode) {
            $('#boxInduk').show();
            $('#labelInduk').text(parentCode);
        } else {
            $('#boxInduk').hide();
        }

        $('#modalInput').modal('show');
    }

    // FUNGSI EDIT
    function editForm(id) {
        $('#formProgram')[0].reset();
        $('#boxInduk').hide();
        
        var url = "{{ url('master/program') }}/" + id;

        $.get(url)
        .done(function(data) {

            $('#titleAction').text('Edit');
            $('#labelLevel').text(data.level);

            $('#inputParentId').val(data.parent_id);
            $('#kode_program').val(data.kode_program);
            $('#nama_program').val(data.nama_program);

            $('#formProgram').attr('action', url);
            $('#methodField').html('<input type="hidden" name="_method" value="PUT">');

            $('#modalInput').modal('show');

        })
        .fail(function(xhr) {
            alert('Gagal mengambil data. Status: ' + xhr.status);
        });

    }

</script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    if (typeof $ === 'undefined') {
        console.log('jQuery TIDAK ADA');
    } else {
        console.log('jQuery ADA');
    }

});
</script>
@endpush

@push('scripts')
<script>
$(document).ready(function () {

    $('#searchProgram').select2({
        placeholder: 'Cari kode atau nama program...',
        minimumInputLength: 2,
        width: '100%',
        ajax: {
            url: "{{ route('master.program.search') }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data };
            }
        }
    });

    // 🔥 TEST EVENT
    $('#searchProgram').on('select2:select', function (e) {

        const kode = e.params.data.id;

        setTimeout(function() {
            window.location.href = "{{ route('master.program.goto') }}?kode=" + kode;
        }, 100);

    });

});
</script>
@endpush

@push('scripts')
<script>
$(document).ready(function () {

    const params = new URLSearchParams(window.location.search);
    const selectedId = params.get('selected_id');

    if (!selectedId) return;

    const row = $('#table-program tbody tr[data-id="' + selectedId + '"]');

    if (row.length) {

        row.addClass('table-active');

        const y = row[0].getBoundingClientRect().top + window.pageYOffset - 150;

        window.scrollTo({
            top: y,
            behavior: "smooth"
        });

    } else {
        console.log("Row tidak ditemukan di halaman ini");
    }

});
</script>
@endpush

@endsection
<style>
.aksi-wrapper {
    display: grid;
    grid-template-columns: 32px 32px 32px;
    gap: 4px;
    justify-content: center;
    align-items: center;
}

.aksi-btn {
    width: 32px;
    height: 26px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table td, .table th {
    padding: 4px 8px !important;
    font-size: 0.88rem;
    vertical-align: middle !important;
}

form {
    margin: 0 !important;
}
</style>

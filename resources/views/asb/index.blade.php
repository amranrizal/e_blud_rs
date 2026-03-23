@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h3>📊 Master ASB</h3>

        <button class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#modalTambah">
            + Tambah ASB
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Uraian</th>
                    <th>Satuan</th>
                    <th class="text-end">Tarif</th>
                    <th>Rekening</th>
                    <th width="10%">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($datas as $item)
                    <tr>
                        <td>{{ $item->kode }}</td>
                        <td>{{ $item->uraian }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-end">
                            Rp {{ number_format($item->tarif,0,',','.') }}
                        </td>
                        <td>{{ optional($item->rekening)->kode_akun }}</td>
                        <td class="text-center">

                            <!-- Tombol Edit -->
                            <button type="button"
                                class="btn btn-sm btn-outline-warning btn-edit-asb"
                                data-id="{{ $item->id }}"
                                data-kode="{{ $item->kode }}"
                                data-uraian="{{ $item->uraian }}"
                                data-satuan="{{ $item->satuan }}"
                                data-tarif="{{ $item->tarif }}"
                                data-rekening="{{ $item->rekening_id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('asb.destroy', $item->id) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Yakin hapus ASB ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Belum ada ASB
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $datas->links() }}

        </div>
    </div>

</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('asb.store') }}">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah ASB</h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Kode</label>
                        <input type="text" name="kode"
                            class="form-control"
                            value="{{ old('kode') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Uraian</label>
                        <input type="text" name="uraian"
                               class="form-control" 
                               value="{{ old('uraian') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label>Satuan</label>
                        <input type="text" name="satuan"
                               class="form-control" 
                               value="{{ old('satuan') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label>Tarif</label>
                        <input type="number" name="tarif"
                               class="form-control" 
                               value="{{ old('tarif') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label>Rekening</label>
                        <select name="rekening_id"
                                id="select-rekening-asb"
                                class="form-control"
                                required>
                        </select>
                    </div>

                    <input type="hidden"
                           name="tahun"
                           value="{{ $tahun }}">

                </div> <!-- TUTUP modal-body -->

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <form method="POST" id="formEditAsb">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit ASB</h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Kode</label>
                        <input type="text" name="kode"
                               id="edit_kode"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Uraian</label>
                        <input type="text" name="uraian"
                               id="edit_uraian"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Satuan</label>
                        <input type="text" name="satuan"
                               id="edit_satuan"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Tarif</label>
                        <input type="number" name="tarif"
                               id="edit_tarif"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Rekening</label>
                        <select name="rekening_id"
                                id="edit_rekening"
                                class="form-control"
                                required>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    let oldRekening = "{{ old('rekening_id') }}";

    // INIT SELECT2
    $('#select-rekening-asb').select2({
        dropdownParent: $('#modalTambah'),
        width: '100%',
        placeholder: 'Cari rekening belanja...',
        minimumInputLength: 2,
        ajax: {
            url: "{{ route('master.rekening.search') }}",
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

    // 🔥 Kalau ada old rekening, ambil datanya lalu set setelah select2 siap
    if (oldRekening) {

        $.ajax({
            url: "{{ route('rekening.byId', ':id') }}"
                    .replace(':id', oldRekening),
            type: "GET",
            success: function(data) {
                if (data) {
                    let option = new Option(data.text, data.id, true, true);
                    $('#select-rekening-asb')
                        .append(option)
                        .trigger('change');
                }
            }
        });
    }

    // 🔥 Buka modal kalau ada error
    @if ($errors->any() || session('error'))
        $('#modalTambah').modal('show');
    @endif

});

$(document).on('click', '.btn-edit-asb', function () {

    let id      = $(this).data('id');
    let kode    = $(this).data('kode');
    let uraian  = $(this).data('uraian');
    let satuan  = $(this).data('satuan');
    let tarif   = $(this).data('tarif');
    let rekening = $(this).data('rekening');

    $('#edit_kode').val(kode);
    $('#edit_uraian').val(uraian);
    $('#edit_satuan').val(satuan);
    $('#edit_tarif').val(tarif);

    // set action form
    let url = "{{ url('asb') }}/" + id;
    $('#formEditAsb').attr('action', url);

    $('#formEditAsb').attr('action', url);

    // init select2 edit
    $('#edit_rekening').select2({
        dropdownParent: $('#modalEdit'),
        width: '100%',
        ajax: {
            url: "{{ route('master.rekening.search') }}",
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

    // set rekening lama
    if (rekening) {
        $.get("{{ url('/rekening') }}/" + rekening, function(data) {
            if (data) {
                let option = new Option(data.text, data.id, true, true);
                $('#edit_rekening').append(option).trigger('change');
            }
        });
    }

    $('#modalEdit').modal('show');
});
</script>
@endpush

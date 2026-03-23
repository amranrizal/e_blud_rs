@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📐 Master HSPK</h3>

        <button class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#modalTambah">
            + Tambah HSPK
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Uraian</th>
                    <th>Satuan</th>
                    <th class="text-end">Harga Total</th>
                    <th>Rekening</th>
                    <th width="10%">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($datas as $item)
                    <tr>
                        <td>{{ $item->uraian }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($item->harga_total,0,',','.') }}
                        </td>
                        <td>
                            {{ optional($item->rekening)->kode_akun }}
                        </td>
                        <td>
                            <a href="{{ route('hspk.show',$item->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada HSPK
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
        <form method="POST" action="{{ route('hspk.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah HSPK</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label>Uraian</label>
                        <input type="text" name="uraian"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Satuan</label>
                        <input type="text" name="satuan"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Rekening</label>
                        <select name="rekening_id"
                                id="select-rekening"
                                class="form-select"
                                required>
                            @foreach($rekeningBelanja as $rek)
                                <option value="{{ $rek->id }}">
                                    {{ $rek->kode_akun }} - {{ $rek->nama_akun ?? '' }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <input type="hidden"
                           name="tahun"
                           value="{{ $tahun }}">

                </div>
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    $('#select-rekening').select2({
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

});
</script>
@endpush



@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h3 class="fw-bold mb-3">
        📐 Detail HSPK
    </h3>

    <div class="card mb-4">
        <div class="card-body">

            <h5>{{ $hspk->uraian }}</h5>
            <p>
                Satuan: {{ $hspk->satuan }} <br>
                Rekening: {{ optional($hspk->rekening)->kode_akun }} <br>
                Total:
                <strong class="text-success">
                    Rp {{ number_format($hspk->harga_total,0,',','.') }}
                </strong>
            </p>

        </div>
    </div>

    {{-- WARNING JIKA BELUM ADA ITEM --}}
    @if($hspk->items->count() == 0)
        <div class="alert alert-warning">
            HSPK ini belum memiliki komponen SSH.
            Tambahkan minimal 1 SSH agar harga dapat dihitung.
        </div>
    @endif

    {{-- TABEL ITEM --}}
    <div class="card">
        <div class="card-body">

            <h6>Komponen SSH</h6>

            <table class="table table-bordered align-middle">
                <thead>
                <tr>
                    <th>SSH</th>
                    <th>Koefisien</th>
                    <th>Harga SSH</th>
                    <th>Subtotal</th>
                    <th width="5%">#</th>
                </tr>
                </thead>
                <tbody>
                @foreach($hspk->items as $item)
                    <tr>
                        <td>{{ $item->ssh->uraian }}</td>
                        <td>{{ $item->koefisien }}</td>
                        <td>
                            Rp {{ number_format($item->ssh->harga,0,',','.') }}
                        </td>
                        <td>
                            Rp {{ number_format($item->koefisien * $item->ssh->harga,0,',','.') }}
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('hspk.hapusItem',$item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    x
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{-- FORM TAMBAH ITEM --}}
            <hr>

            <form method="POST"
                  action="{{ route('hspk.tambahItem',$hspk->id) }}">
                @csrf

                <div class="row g-2">
                    <div class="col-md-6">
                        <select name="standar_harga_id"
                                id="select-ssh"
                                class="form-select" required>
                            <option value="">Pilih SSH</option>
                            @foreach($sshList as $ssh)
                                <option value="{{ $ssh->id }}">
                                    {{ $ssh->uraian }} - Rp {{ number_format($ssh->harga,0,',','.') }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-3">
                        <input type="number"
                               name="koefisien"
                               step="0.0001"
                               min="0.0001"
                               class="form-control"
                               placeholder="Koefisien"
                               required>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-success w-100">
                            Tambah
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#select-ssh').select2({
        placeholder: 'Cari SSH...',
        width: '100%'
    });
});
</script>
@endpush

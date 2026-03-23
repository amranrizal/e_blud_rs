@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Standar Harga (Read Only)</h4>
    <form method="GET" action="{{ route('standar-harga.readonly') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    class="form-control"
                    placeholder="Cari kode barang / uraian / spesifikasi">
            </div>

            <div class="col-md-3">
                <input
                    type="number"
                    name="tahun"
                    value="{{ request('tahun') }}"
                    class="form-control"
                    placeholder="Tahun">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>

                <a href="{{ route('standar-harga.readonly') }}" class="btn btn-secondary w-100">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Kode Kelompok</th>
                <th>Kode Barang</th>
                <th>Uraian</th>
                <th>Spesifikasi</th>
                <th>Satuan</th>
                <th class="text-end">Harga</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $item)
        <tr>
            <td>{{ $item->kode_kelompok }}</td>
            <td>{{ $item->kode_barang }}</td>
            <td>{{ $item->uraian }}</td>
            <td>{{ $item->spesifikasi }}</td>
            <td>{{ $item->satuan }}</td>
            <td class="text-end">
                {{ number_format($item->harga, 0, ',', '.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">
                Data tidak ditemukan
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $data->links() }}
    </div>

</div>
@endsection

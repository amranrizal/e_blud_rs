@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Pembiayaan (Akun 6)</h1>
    <p class="mb-4">Kelola data Penerimaan Pembiayaan (SILPA, Pencairan Dana Cadangan) dan Pengeluaran Pembiayaan.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Tabel Rincian Pembiayaan</h6>
            
            {{-- Tombol Tambah --}}
            <a href="{{ route('pembiayaan.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Pembiayaan
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr style="background-color: #f8f9fc;">
                            <th width="5%">No</th>
                            <th width="15%">Kode Rekening</th>
                            <th>Uraian</th>
                            <th width="10%">Vol / Sat</th>
                            <th width="15%">Nilai (Rp)</th>
                            <th width="15%">Total (Rp)</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        {{-- Pembeda Warna Baris 6.1 (Hijau Tipis) dan 6.2 (Merah Tipis) --}}
                        @php
                            $bgClass = substr($item->kode_akun, 0, 3) == '6.1' ? 'style="background-color:#e8f5e9"' : 'style="background-color:#ffebee"';
                        @endphp

                        <tr {!! $bgClass !!}>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->kode_akun }}</strong><br>
                                <small class="text-muted">
                                    {{ substr($item->kode_akun, 0, 3) == '6.1' ? 'Penerimaan' : 'Pengeluaran' }}
                                </small>
                            </td>
                            <td>{{ $item->uraian }}</td>
                            <td>
                                {{ $item->volume + 0 }} {{-- +0 supaya tidak muncul .00 di integer --}} 
                                <br>
                                <small>{{ $item->satuan }}</small>
                            </td>
                            <td class="text-right">{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                            <td class="text-center">
                                {{-- Tombol Edit (Opsional, buat route edit dulu jika mau dipakai) --}}
                                <a href="{{ route('pembiayaan.edit', $item->id) }}" class="btn btn-warning btn-sm btn-circle" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('pembiayaan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini? Data SILPA di laporan akan berubah!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-circle" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Script DataTables (Jika belum ada di Layout Utama) --}}
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endsection
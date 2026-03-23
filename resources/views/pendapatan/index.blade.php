@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Target Pendapatan (RBA)</h1>
        
        <!-- Filter Tahun -->
        <form action="{{ route('pendapatan.index') }}" method="GET" class="d-flex">
            <select name="tahun" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <a href="{{ route('pendapatan.create') }}" class="btn btn-primary btn-sm shadow-sm text-nowrap">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Target
            </a>
        </form>
    </div>

    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Target Pendapatan {{ $tahun }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPendapatan, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rincian Target Pendapatan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="15%">Kode Akun</th>
                            <th>Uraian Pendapatan</th>
                            <th width="10%" class="text-center">Vol</th>
                            <th width="10%" class="text-center">Satuan</th>
                            <th width="15%" class="text-end">Tarif / Harga</th>
                            <th width="15%" class="text-end">Total Target</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td class="font-monospace">{{ $item->kode_akun }}</td>
                                <td class="fw-bold">{{ $item->uraian }}</td>
                                <td class="text-center">{{ number_format($item->volume, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->satuan }}</td>
                                <td class="text-end">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pendapatan.edit', $item->id) }}" class="btn btn-warning btn-sm btn-circle" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('pendapatan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-circle" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada data target pendapatan tahun {{ $tahun }}.<br>
                                    Silakan klik tombol <b>Tambah Target</b>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
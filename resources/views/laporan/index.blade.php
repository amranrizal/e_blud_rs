@extends('layouts.app') 

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan RBA (Pendapatan, Belanja & Pembiayaan)</h1>
    </div>

    <!-- Filter Tahun & Tombol Cetak -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter & Cetak</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="form-inline mb-3">
                <label class="mr-2">Tahun Anggaran:</label>
                <select name="tahun" class="form-control mr-2" onchange="this.form.submit()">
                    @for($i = date('Y'); $i >= date('Y')-2; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                {{-- Tombol Refresh --}}
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> Refresh Data</button>
            </form>

            <hr>

            {{-- Form Cetak PDF --}}
            <form action="{{ route('laporan.cetakRbaFull') }}" method="GET" target="_blank">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="form-group">
                    <label>Tanggal Cetak:</label>
                    <input type="date" name="tanggal_cetak" class="form-control w-25 mb-2" value="{{ date('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-danger btn-lg">
                    <i class="fas fa-file-pdf"></i> Download PDF Laporan RBA Full
                </button>
            </form>
        </div>
    </div>

    <!-- Content Row (Ringkasan Angka) -->
    <div class="row">

        <!-- Pendapatan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalPendapatan, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Belanja Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Belanja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalBelanja, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembiayaan Netto Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pembiayaan Netto</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($pembiayaanNetto, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SILPA Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Estimasi SILPA</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($silpaAkhir, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
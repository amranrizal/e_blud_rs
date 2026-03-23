@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark fw-bold mb-0">🔗 Mapping Renja & Pagu</h3>
        
        <!-- Filter Tahun -->
        <form method="GET" class="d-flex align-items-center">
            <label class="me-2 fw-bold">Tahun:</label>
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                @for($i = date('Y'); $i <= date('Y')+1; $i++)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="row">
        @foreach($units as $unit)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <!-- Ambil huruf depan dari nama_unit -->
                                {{ substr($unit->nama_unit, 0, 1) }}
                            </div>
                            <div class="ms-3">
                                <!-- Tampilkan Nama Unit -->
                                <h5 class="card-title fw-bold mb-0">{{ $unit->nama_unit }}</h5>
                                <!-- Tampilkan Kode Unit -->
                                <small class="text-muted">Kode: {{ $unit->kode_unit }}</small>
                            </div>
                        </div>
                        
                        <div class="p-2 bg-light rounded mb-3">
                            <small class="text-muted d-block">Total Pagu {{ $tahun }}</small>
                            <h5 class="fw-bold text-success mb-0">
                                Rp {{ number_format($unit->paguIndikatifs->sum('pagu'), 0, ',', '.') }}
                            </h5>
                        </div>

                        <!-- Link pakai ID -->
                        <a href="{{ route('pagu.edit', ['id' => $unit->id, 'tahun' => $tahun]) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-cog"></i> Atur Kegiatan
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')

{{-- 1. HEADER LOGO / NAMA RS --}}
<div class="row mb-4">
    <div class="col-12 text-right">
        <h5 class="font-weight-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">
            RUMAH SAKIT UMUM DAERAH K.H. HAYYUNG
        </h5>
    </div>
</div>

{{-- 2. INFO CARDS (WIDGET CEPAT) --}}
{{-- Hanya muncul jika variabel dikirim dari route (agar tidak error) --}}
@if(isset($total_program) && isset($total_rekening))
<div class="row mb-4">
    
    {{-- CARD 1: MASTER PROGRAM --}}
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-primary shadow-sm h-100 py-2 bg-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Master Program
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_program }} Data</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('master.program.index') }}" class="stretched-link"></a> {{-- Klik seluruh kartu --}}
            </div>
        </div>
    </div>

    {{-- CARD 2: MASTER REKENING --}}
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-success shadow-sm h-100 py-2 bg-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Master Rekening
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_rekening }} Akun</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('master.rekening.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    {{-- CARD: STANDAR HARGA (READ ONLY - USER & BOSS) --}}
    @if(in_array(auth()->user()->role, ['user','boss']))
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-info shadow-sm h-100 py-2 bg-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Standar Harga
                        </div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            Referensi Harga (Read Only)
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>

                <a href="{{ route('standar-harga.readonly') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
    @endif


    {{-- CARD 3: TOTAL UNIT (Khusus Admin) --}}
    @if(auth()->user()->role === 'admin')
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-warning shadow-sm h-100 py-2 bg-white">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Unit Kerja
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_unit ?? 0 }} Unit</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('master.unit.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- ... Card Program dll yang sudah ada ... -->

<!-- Card Manajemen User (HANYA ADMIN) -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Total Pengguna
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $jumlahUser }} User
                    </div>
                </div>
                <div class="col-auto">
                    <!-- Icon Orang/User -->
                    <i class="bi bi-people-fill fa-2x text-gray-300" style="font-size: 2rem;"></i>
                </div>
            </div>
            <!-- Tombol Link Cepat -->
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-warning mt-3 text-white w-100">
                <i class="bi bi-gear-fill"></i> Kelola User
            </a>
        </div>
    </div>
</div>
@endif

</div>
@endif

{{-- 3. AREA TENGAH (WELCOME MESSAGE) --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="min-height: 400px; background: white;">
            <div class="card-body d-flex align-items-center justify-content-center text-center">
                
                <div style="opacity: 0.6;">
                    <i class="fas fa-hospital-user fa-5x mb-3 text-gray-300"></i>
                    <h3 class="text-gray-600 font-weight-bold">Selamat Datang, {{ auth()->user()->name }}</h3>
                    <p class="text-gray-400">Sistem Informasi Pengelolaan Keuangan Daerah (E-BLUD)</p>
                    
                    {{-- Tambahan Jam/Tanggal --}}
                    <div class="mt-3 badge badge-light border px-3 py-2">
                        <i class="far fa-calendar-alt mr-2"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- 4. FOOTER COPYRIGHT --}}
<div class="row mt-4 mb-4">
    <div class="col-12 text-left">
        <small class="text-muted font-italic">
            {{ date('Y') }} &copy; SIPD-RI - Kementerian Dalam Negeri Republik Indonesia (Clone by E-BLUD RS)
        </small>
    </div>
</div>

{{-- ========================================================= --}}
{{-- 5. ADMIN OVERRIDE (OPSIONAL - JIKA ADA KONTEKS ANGGARAN) --}}
{{-- ========================================================= --}}
@if(
    auth()->check()
    && auth()->user()->role === 'admin'
    && isset($anggaran)
)
    <hr>

    <div class="row">
        <div class="col-12">
            @include('master.program.partials.admin_override')
        </div>
    </div>
@endif

@endsection
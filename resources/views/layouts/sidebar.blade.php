<!-- Ubah kelas warna jadi sidebar-light-sipd -->
<ul class="navbar-nav sidebar-light-sipd sidebar accordion" id="accordionSidebar">

    <!-- 1. LOGO SIPD -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start pl-3" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-layer-group text-primary"></i> 
        </div>
        <div class="sidebar-brand-text mx-3 text-left">
            {{-- Baris 1: Brand Utama --}}
            <div style="line-height: 1.1;">
                {{-- Huruf 'e-' : Abu Gelap, Tebal --}}
                <span class="text-gray-900 font-weight-bolder text-lowercase" style="font-size: 1.4rem; letter-spacing: -1px;">e-</span>
                {{-- Huruf 'BLUD' : Biru, Miring, Tebal --}}
                <span class="text-primary font-italic font-weight-bold" style="font-size: 1.4rem;">BLUD</span>
            </div>
            
            {{-- Baris 2: Subtitle RS (Kecil & Renggang biar elegan) --}}
            <div class="text-primary font-weight-bold font-italic" style="font-size: 0.6rem; letter-spacing: 2px; opacity: 0.8;">
                RUMAH SAKIT
            </div>
        </div>
    </a>

    <!-- ========================================================= -->
    <!-- 2. TOMBOL HOME / DASHBOARD (POSISI TERBAIK DI SINI) -->
    <!-- ========================================================= -->
    <!-- Saya pindahkan dari bawah ke sini biar navigasi enak -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-home"></i> <!-- Ikon Rumah -->
            <span>Home / Dashboard</span>
        </a>
    </li>

    <!-- Divider (Garis Pemisah) -->
    <hr class="sidebar-divider my-0">

    <!-- 3. Menu Item: Pengumuman -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Pengumuman</span></a>
    </li>

    <!-- 4. Menu Item: Referensi (Dropdown) -->
    <li class="nav-item {{ request()->routeIs('master.ref.index*') ? 'active' : '' }}">
        
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReferensi"
            aria-expanded="{{ request()->routeIs('master.ref.index*') ? 'true' : 'false' }}" 
            aria-controls="collapseReferensi">
            <i class="fas fa-fw fa-book"></i>
            <span>Referensi</span>
        </a>

        <div id="collapseReferensi" class="collapse {{ request()->routeIs('master.ref.index*') ? 'show' : '' }}" 
            aria-labelledby="headingReferensi" data-parent="#accordionSidebar">
            
            {{-- JURUS PAKSA: --}}
            {{-- 1. padding-bottom: 0 !important (Paksa nol) --}}
            {{-- 2. overflow: hidden (Potong apapun yang lewat batas) --}}
            <div class="bg-white collapse-inner rounded" style="padding-top: 0.5rem; padding-bottom: 0 !important; overflow: hidden;">
                
                <h6 class="collapse-header">Master Data:</h6>

                <!-- ITEM-ITEM ATAS BIARKAN SAMA SEPERTI SEBELUMNYA -->
                @php $urlBidang = route('master.ref.index', ['level' => 'bidang_urusan']); @endphp
                <a class="collapse-item {{ request()->fullUrlIs($urlBidang) ? 'active' : '' }}" href="{{ $urlBidang }}">
                <i class="fas fa-circle mr-2 {{ request()->fullUrlIs($urlBidang) ? 'text-primary' : 'text-gray-300' }}" style="font-size: 6px;"></i> Bidang Urusan
                </a>

                @php $urlProgram = route('master.ref.index', ['level' => 'program']); @endphp
                <a class="collapse-item {{ request()->fullUrlIs($urlProgram) ? 'active' : '' }}" href="{{ $urlProgram }}">
                <i class="fas fa-circle mr-2 {{ request()->fullUrlIs($urlProgram) ? 'text-primary' : 'text-gray-300' }}" style="font-size: 6px;"></i> Program
                </a>

                @php $urlKegiatan = route('master.ref.index', ['level' => 'kegiatan']); @endphp
                <a class="collapse-item {{ request()->fullUrlIs($urlKegiatan) ? 'active' : '' }}" href="{{ $urlKegiatan }}">
                <i class="fas fa-circle mr-2 {{ request()->fullUrlIs($urlKegiatan) ? 'text-primary' : 'text-gray-300' }}" style="font-size: 6px;"></i> Kegiatan
                </a>

                @php $urlSub = route('master.ref.index', ['level' => 'sub_kegiatan']); @endphp
                <a class="collapse-item {{ request()->fullUrlIs($urlSub) ? 'active' : '' }}" href="{{ $urlSub }}">
                <i class="fas fa-circle mr-2 {{ request()->fullUrlIs($urlSub) ? 'text-primary' : 'text-gray-300' }}" style="font-size: 6px;"></i> Sub Kegiatan
                </a>
                
                <hr class="sidebar-divider my-2">

                @php $urlAkun = route('master.ref.index', ['level' => 'akun']); @endphp
                <a class="collapse-item {{ request()->fullUrlIs($urlAkun) ? 'active' : '' }}" href="{{ $urlAkun }}">
                    <i class="fas fa-circle mr-2 {{ request()->fullUrlIs($urlAkun) ? 'text-primary' : 'text-gray-300' }}" style="font-size: 6px;"></i> Akun (Rekening)
                </a>

                <!-- 6. SUMBER DANA -->
                {{-- UPDATE TERAKHIR: --}}
                {{-- Tambahkan margin-bottom: 0 !important pada item terakhir --}}
                {{-- Tambahkan padding-bottom sedikit (0.5rem) agar teks tidak terpotong, tapi box habis disitu --}}
                <a class="collapse-item" href="#" style="margin-bottom: 0 !important; padding-bottom: 0.5rem;">
                    <i class="fas fa-circle mr-2 text-gray-300" style="font-size: 6px;"></i> Sumber Dana
                </a>
                
            </div>
        </div>
    </li>

    <!-- Divider Pemisah Kelompok -->
    <hr class="sidebar-divider my-2">
    
    <!-- JUDUL KELOMPOK (OPSIONAL) -->
    <div class="sidebar-heading">
        Transaksi
    </div>

    <!-- ========================================================= -->
    <!-- MENU PENGANGGARAN (SSH & Mapping Pagu)                    -->
    <!-- ========================================================= -->
    <li class="nav-item {{ request()->routeIs('standar-harga*', 'pagu*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePenganggaran"
            aria-expanded="{{ request()->routeIs('standar-harga*', 'pagu*') ? 'true' : 'false' }}"
            aria-controls="collapsePenganggaran">
            <i class="nav-icon fas fa-fw fa-cogs"></i> {{-- Icon Cogs cocok untuk Master/Setup --}}
            <span>Transaksi Penganggaran</span>
        </a>

        {{-- ID Container ini HARUS SAMA dengan data-target diatas --}}
        <div id="collapsePenganggaran" class="collapse {{ request()->routeIs('standar-harga*', 'pagu*') ? 'show' : '' }}"
            aria-labelledby="headingPenganggaran" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Master Data:</h6>

                    {{-- 1. STANDAR HARGA (SSH) --}}
                    <a class="collapse-item {{ request()->routeIs('standar-harga*') ? 'active' : '' }}"
                    href="{{ route('standar-harga.index') }}">
                    <i class="fas fa-fw fa-tags mr-1"></i> Standar Harga (SSH)
                    </a>

                    {{-- 2. HSPK --}}
                    <a href="{{ route('hspk.index') }}"
                    class="nav-link">
                        <i class="fas fa-drafting-compass me-2"></i>
                        HSPK
                    </a>

                    {{-- 3. ASB --}}
                    <a href="{{ route('asb.index') }}"
                    class="nav-link {{ request()->routeIs('asb.*') ? 'active' : '' }}">
                        <i class="fas fa-balance-scale me-2"></i>
                        <span>ASB</span>
                    </a>

                    {{-- 4. SBU --}}
                     <a href="{{ route('sbu.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>SBU</p>
                    </a>


                {{-- 4. MAPPING RENJA (PAGU) --}}
                <a class="collapse-item {{ request()->routeIs('pagu*') ? 'active' : '' }}"
                href="{{ route('pagu.index') }}">
                <i class="fas fa-fw fa-project-diagram mr-1"></i>
                <span>Pagu Indikatif</span>
                </a>

                <a href="{{ route('renja.index') }}" class="nav-link">
                <i class="fas fa-list"></i>
                <span>RENJA BLUD</span>
                </a>
            </div>
        </div>
    </li>

    <!-- ========================================================= -->
    <!-- MENU TRANSAKSI RBA (Pendapatan, Belanja, Pembiayaan)      -->
    <!-- ========================================================= -->
    <li class="nav-item {{ request()->routeIs('pendapatan*', 'budget*', 'pembiayaan*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRBA"
            aria-expanded="{{ request()->routeIs('pendapatan*', 'budget*', 'pembiayaan*') ? 'true' : 'false' }}" 
            aria-controls="collapseRBA">
            <i class="nav-icon fas fa-fw fa-money-bill-wave"></i>
            <span>Transaksi RBA</span>
        </a>
        
        {{-- ID ini harus beda dengan menu Penganggaran (collapseAnggaran) --}}
        <div id="collapseRBA" class="collapse {{ request()->routeIs('pendapatan*', 'budget*', 'pembiayaan*') ? 'show' : '' }}" 
            aria-labelledby="headingRBA" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Input Anggaran:</h6>
                
                {{-- 1. PENDAPATAN (Akun 4) --}}
                <a class="collapse-item {{ request()->routeIs('pendapatan*') ? 'active' : '' }}" 
                href="{{ route('pendapatan.index') }}">
                <i class="fas fa-fw fa-hand-holding-usd mr-1 text-success"></i> Pendapatan (Akun 4)
                </a>

                {{-- 2. BELANJA (Akun 5) --}}
                <a class="collapse-item {{ request()->routeIs('budget*') ? 'active' : '' }}" 
                href="{{ route('budget.index') }}">
                <i class="fas fa-fw fa-shopping-cart mr-1 text-danger"></i> Belanja (Akun 5)
                </a>

                {{-- 3. PEMBIAYAAN (Akun 6) - BARU --}}
                <a class="collapse-item {{ request()->routeIs('pembiayaan*') ? 'active' : '' }}" 
                href="{{ route('pembiayaan.index') }}">
                <i class="fas fa-fw fa-university mr-1 text-primary"></i> Pembiayaan (Akun 6)
                </a>
                
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Output:</h6>

                {{-- LINK LAPORAN RBA --}}
                <a class="collapse-item {{ request()->routeIs('laporan*') ? 'active' : '' }}" 
                href="{{ route('laporan.index') }}">
                <i class="fas fa-fw fa-print mr-1"></i> Cetak RBA Full
                </a>

            </div>
        </div>
    </li>

    <!--Pengaturan Aplikasi--->
    <!-- Pastikan Pengaturan Sistem dibungkus li class="nav-item" -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('settings.index') }}">
            <i class="fas fa-cogs"></i>
            <span>Pengaturan Sistem</span>
        </a>
    </li>

    <!-- Spacer -->
    <div style="flex-grow: 1;"></div>

    <!-- Tombol Panduan -->
    <div class="mb-4 mt-4 text-center">
        <a href="#" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 20px; width: 80%;">
            Panduan Penggunaan
        </a>
    </div>

</ul>
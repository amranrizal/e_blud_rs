@extends('layouts.app')

@section('content')

<!-- Judul Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    <span class="badge badge-primary px-3 py-2">Total Data: {{ count($data) }}</span>
</div>

<!-- CONTAINER UTAMA -->
<div class="row">
    <div class="col-12">

        {{-- 
           LOGIC GROUPING (PENGELOMPOKAN) 
           Kita kelompokkan data berdasarkan ID Parent-nya.
           Jadi Induknya cuma muncul sekali, anaknya berbaris di bawahnya.
        --}}
        @php
            // Grouping data berdasarkan Parent ID
            $groupedData = $data->groupBy('parent_id');
        @endphp

        @forelse($groupedData as $parentId => $items)
            
            {{-- AMBIL DATA INDUK DARI ITEM PERTAMA DI GROUP INI --}}
            @php 
                $firstItem = $items->first();
                $parent = $firstItem->parent;
                
                // Logic Silsilah untuk Breadcrumb (Jejak di atas Header)
                $breadcrumb = '';
                if($level == 'program') {
                    // Induknya Bidang, Kakeknya Urusan
                    $breadcrumb = ($parent->parent->kode_program ?? '') . ' ' . ($parent->parent->nama_program ?? '') . ' > ';
                }
                elseif($level == 'kegiatan') {
                    // Induknya Program, Kakeknya Bidang...
                    $breadcrumb = ($parent->parent->kode_program ?? '') . ' ' . ($parent->parent->nama_program ?? '') . ' > ' . 
                                  ($parent->kode_program ?? '') . ' ' . ($parent->nama_program ?? '') . ' > ';
                }
                elseif($level == 'sub_kegiatan') {
                    // Induknya Kegiatan...
                    $breadcrumb = '... > ' . ($parent->parent->kode_program ?? '') . ' ' . ($parent->parent->nama_program ?? '') . ' > ' . 
                                  ($parent->kode_program ?? '') . ' ' . ($parent->nama_program ?? '') . ' > ';
                }
            @endphp

            <!-- KARTU PER KELOMPOK (INDUK) -->
            <!-- KARTU PER KELOMPOK (INDUK) -->
    <div class="card shadow mb-4 border-left-primary">
    
        <!-- HEADER: MENAMPILKAN INDUKNYA -->
        <div class="card-header py-3 bg-light">
            <!-- Breadcrumb Kecil -->
            @if(!empty($breadcrumb))
                <div class="text-xs text-uppercase text-gray-500 mb-1 font-weight-bold">
                    {{ $breadcrumb }}
                </div>
            @endif

            <h6 class="m-0 font-weight-bold text-primary">
                @if($parent)
                    {{-- JIKA PUNYA INDUK (Level 2 s/d 6) --}}
                    <i class="fas fa-folder-open mr-2"></i>
                    [{{ $parent->kode_program ?? $parent->kode_akun }}] 
                    {{ $parent->nama_program ?? $parent->nama_akun }}
                @else
                    {{-- JIKA TIDAK PUNYA INDUK (Level 1 / Akun Utama) --}}
                    <i class="fas fa-university mr-2"></i>
                    AKUN UTAMA (LEVEL 1)
                @endif
            </h6>
        </div>

    <!-- BODY: MENAMPILKAN LIST ANAK-ANAKNYA -->
    <!-- ... (codingan list ke bawah tetap sama) ... -->

                <!-- BODY: MENAMPILKAN LIST ANAK-ANAKNYA -->
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($items as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center hover-bg-gray">
                                <div>
                                    <!-- Kode Anak -->
                                    <span class="badge badge-secondary badge-pill mr-2">
                                        {{ $item->kode_program ?? $item->kode_akun }}
                                    </span>
                                    
                                    <!-- Nama Anak -->
                                    <span class="text-dark font-weight-bold">
                                        {{ $item->nama_program ?? $item->nama_akun}}
                                    </span>
                                </div>

                                <!-- Tombol Aksi (Opsional, buat edit nanti) -->
                                <!-- <button class="btn btn-sm btn-circle btn-light"><i class="fas fa-ellipsis-v"></i></button> -->
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        @empty
            <!-- JIKA DATA KOSONG -->
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i> Belum ada data untuk referensi ini.
            </div>
        @endforelse

    </div>
</div>

<style>
    /* Efek Hover biar enak dilihat */
    .hover-bg-gray:hover {
        background-color: #f8f9fc;
        cursor: default;
    }
</style>

@endsection
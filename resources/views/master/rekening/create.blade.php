@extends('layouts.app')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Rekening Baru</h6>
    </div>

    {{-- ALERT VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger m-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-body">

        @php
            $urlParentId = request()->query('parent_id');
            $parentData  = null;
            $nextLevel   = 'Akun';
            $prefixKode  = '';

            if ($urlParentId) {
                $parentData = \App\Models\Rekening::find($urlParentId);
            }

            if ($parentData) {
                switch ($parentData->level) {
                    case 'Akun': $nextLevel = 'Kelompok'; break;
                    case 'Kelompok': $nextLevel = 'Jenis'; break;
                    case 'Jenis': $nextLevel = 'Objek'; break;
                    case 'Objek': $nextLevel = 'Rincian Objek'; break;
                    case 'Rincian Objek': $nextLevel = 'Sub Rincian Objek'; break;
                    default: $nextLevel = 'Sub Rincian Objek'; break;
                }
                $prefixKode = $parentData->kode_akun . '.';
            }
        @endphp

        {{-- 🚫 LEVEL MAKSIMAL --}}
        @if ($parentData && $parentData->level === 'Sub Rincian Objek')

            <div class="alert alert-warning">
                <i class="fas fa-lock"></i>
                <strong>Level maksimal tercapai.</strong><br>
                Rekening <strong>{{ $parentData->kode_akun }}</strong>
                sudah berada di level terakhir dan tidak bisa ditambahkan turunan.
            </div>

            <a href="{{ route('master.rekening.index') }}" class="btn btn-secondary">
                Kembali
            </a>

        @else

        <form action="{{ route('master.rekening.store') }}" method="POST">
            @csrf

            @if ($parentData)
                <input type="hidden" name="parent_id" value="{{ $parentData->id }}">

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Menambahkan <strong>{{ $nextLevel }}</strong> di bawah:<br>
                    <strong>{{ $parentData->kode_akun }} - {{ $parentData->nama_akun }}</strong>
                </div>
            @endif

            {{-- LEVEL --}}
            <div class="form-group">
                <label>Level Rekening</label>
                <input type="text"
                    class="form-control bg-light"
                    value="{{ $nextLevel }}"
                    readonly>
            </div>


            {{-- KODE --}}
            <div class="form-group">
                <label>Kode Rekening</label>
                <div class="input-group">
                    @if ($prefixKode)
                        <div class="input-group-prepend">
                            <span class="input-group-text font-weight-bold">
                                {{ $prefixKode }}
                            </span>
                        </div>
                    @endif

                    <input type="text"
                           name="kode_akun_suffix"
                           value="{{ old('kode_akun_suffix') }}"
                           class="form-control @error('kode_akun_suffix') is-invalid @enderror"
                           placeholder="Contoh: 03"
                           required autofocus>

                    @error('kode_akun_suffix')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- NAMA --}}
            <div class="form-group">
                <label>Uraian Akun</label>
                <input type="text"
                       name="nama_akun"
                       value="{{ old('nama_akun') }}"
                       class="form-control @error('nama_akun') is-invalid @enderror"
                       required>

                @error('nama_akun')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('master.rekening.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </form>

        @endif
    </div>
</div>

@endsection

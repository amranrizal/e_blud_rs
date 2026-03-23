@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Program/Kegiatan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            
            <form action="{{ route('master.program.update', $program->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- 1. PARENT ID --}}
                <div class="form-group">
                    <label class="font-weight-bold">Induk (Parent)</label>
                    <select name="parent_id" class="form-control select2">
                        <option value="">-- Level Tertinggi (Urusan) --</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}" 
                                {{-- PERHATIKAN: Kita pakai parent_id --}}
                                {{ old('parent_id', $program->parent_id) == $p->id ? 'selected' : '' }}>
                                
                                {{-- PERHATIKAN: Tampilkan kode_program & nama_program --}}
                                [{{ $p->kode_program }}] {{ $p->nama_program }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    {{-- 2. KODE PROGRAM (Sesuai Database) --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Kode</label>
                            {{-- Name ganti jadi kode_program --}}
                            <input type="text" name="kode_program" 
                                   class="form-control" 
                                   {{-- Value ambil dari kode_program --}}
                                   value="{{ old('kode_program', $program->kode_program) }}">
                        </div>
                    </div>

                    {{-- 3. NAMA PROGRAM (Sesuai Database) --}}
                    <div class="col-md-9">
                        <div class="form-group">
                            <label class="font-weight-bold">Uraian Nama</label>
                            {{-- Name ganti jadi nama_program --}}
                            <input type="text" name="nama_program" 
                                   class="form-control" 
                                   {{-- Value ambil dari nama_program --}}
                                   value="{{ old('nama_program', $program->nama_program) }}">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
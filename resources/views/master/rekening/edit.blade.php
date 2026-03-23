@extends('layouts.app')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">Edit Rekening</h6>
    </div>

    {{-- ALERT GLOBAL VALIDASI --}}
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
        <form action="{{ route('master.rekening.update', $rekening->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- INDUK REKENING --}}
            <div class="form-group">
                <label>Induk Rekening</label>
                <select name="parent_id"
                        class="form-control"
                        {{ $rekening->children->count() ? 'disabled' : '' }}>
                    <option value="">-- Tanpa Induk --</option>
                    @foreach($allRekening as $induk)
                        <option value="{{ $induk->id }}"
                            {{ old('parent_id', $rekening->parent_id) == $induk->id ? 'selected' : '' }}>
                            {{ $induk->kode_akun }} - {{ $induk->nama_akun }}
                        </option>
                    @endforeach
                </select>

                @if($rekening->children->count())
                    {{-- disabled tidak ikut submit --}}
                    <input type="hidden" name="parent_id" value="{{ $rekening->parent_id }}">
                    <small class="text-muted">
                        Parent tidak bisa diubah karena rekening ini memiliki turunan.
                    </small>
                @endif
            </div>

            <div class="row">
                {{-- KODE REKENING --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Rekening</label>
                        <input type="text"
                               name="kode_akun"
                               value="{{ old('kode_akun', $rekening->kode_akun) }}"
                               class="form-control @error('kode_akun') is-invalid @enderror"
                               {{ $rekening->children->count() ? 'readonly' : '' }}
                               required>

                        @error('kode_akun')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        @if($rekening->children->count())
                            <small class="text-muted">
                                Kode rekening dikunci karena memiliki turunan.
                            </small>
                        @endif
                    </div>
                </div>

                {{-- LEVEL (AUTO-LOCK, READONLY) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Level</label>
                        <input type="text"
                               class="form-control bg-light"
                               value="{{ $rekening->level }}"
                               readonly>
                        <small class="text-muted">
                            Level ditentukan otomatis oleh sistem.
                        </small>
                    </div>
                </div>
            </div>

            {{-- NAMA AKUN --}}
            <div class="form-group">
                <label>Uraian Akun</label>
                <input type="text"
                       name="nama_akun"
                       value="{{ old('nama_akun', $rekening->nama_akun) }}"
                       class="form-control"
                       required>
            </div>

            {{-- ACTION --}}
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Update Data
            </button>
            <a href="{{ route('master.rekening.index') }}" class="btn btn-secondary">
                Batal
            </a>
        </form>
    </div>
</div>

@endsection
@push('scripts')
<script>
$(function() {

    $('.select-parent').select2({
        width: '100%',
        placeholder: 'Pilih Induk Rekening'
    });

});
</script>
@endpush


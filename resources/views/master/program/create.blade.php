@extends('layouts.app')

@section('content')

<div class="container">
    <h4>Tambah Program</h4>

    <form action="{{ route('master.program.store') }}" method="POST">
        @csrf

        {{-- Parent Hidden --}}
        @if(isset($parentId))
            <input type="hidden" name="parent_id" value="{{ $parentId }}">
        @endif

        <div class="mb-3">
            <label class="form-label">Kode Program</label>
            <input type="text" name="kode_program" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Program</label>
            <input type="text" name="nama_program" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Level</label>
            <select name="level" class="form-control" required>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Level 4</option>
                <option value="5">Level 5</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan
        </button>

        <a href="{{ route('master.program.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </form>
</div>

@endsection
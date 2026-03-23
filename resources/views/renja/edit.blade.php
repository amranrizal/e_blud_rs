@extends('layouts.app')

@section('content')

<div class="container-fluid">

<h3 class="mb-4 fw-bold">
Edit RENJA BLUD
</h3>

<div class="card shadow-sm border-0">

<div class="card-body">

<form action="{{ route('renja.update',$renja->id) }}" method="POST">

@csrf
@method('PUT')

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">Unit Kerja</label>

<select name="unit_id" class="form-control">

@foreach($units as $u)

<option value="{{ $u->id }}"
{{ $renja->unit_id == $u->id ? 'selected' : '' }}>

{{ $u->kode_unit }} - {{ $u->nama_unit }}

</option>

@endforeach

</select>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">Sub Kegiatan</label>

<select name="sub_kegiatan_id" class="form-control">

@foreach($subKegiatans as $sub)

<option value="{{ $sub->id }}"
{{ $renja->sub_kegiatan_id == $sub->id ? 'selected' : '' }}>

{{ $sub->kode_program }} - {{ $sub->nama_program }}

</option>

@endforeach

</select>

</div>

</div>

<div class="mb-3">

<label class="fw-bold">Indikator Kinerja</label>

<textarea name="indikator_kinerja"
class="form-control">{{ $renja->indikator_kinerja }}</textarea>

</div>

<div class="row">

<div class="col-md-4">

<label class="fw-bold">Target</label>

<input type="text"
name="target"
class="form-control"
value="{{ $renja->target }}">

</div>

<div class="col-md-4">

<label class="fw-bold">Satuan</label>

<input type="text"
name="satuan"
class="form-control"
value="{{ $renja->satuan }}">

</div>

<div class="col-md-4">

<label class="fw-bold">Pagu Rencana</label>

<input type="number"
name="pagu_rencana"
class="form-control"
value="{{ $renja->pagu_rencana }}">

</div>

</div>

<div class="mt-3">

<button class="btn btn-primary">

<i class="fas fa-save"></i>
Update RENJA

</button>

<a href="{{ route('renja.index') }}" class="btn btn-secondary">

Batal

</a>

</div>

</form>

</div>

</div>

</div>

@endsection
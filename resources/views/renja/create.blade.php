@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<div class="container-fluid">

<h3 class="mb-4 fw-bold">
Tambah RENJA BLUD
</h3>

<div class="card shadow-sm border-0">

<div class="card-body">

<form action="{{ route('renja.store') }}" method="POST">

@csrf

<div class="row">

<div class="col-md-6 mb-3">
<label class="fw-bold">Unit Kerja</label>

<select name="unit_id" class="form-control select2" required>

<option value="">-- Pilih Unit --</option>

@foreach($units as $u)

<option value="{{ $u->id }}">
{{ $u->kode_unit }} - {{ $u->nama_unit }}
</option>

@endforeach

</select>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">Sub Kegiatan</label>

<select name="sub_kegiatan_id"
        id="sub_kegiatan"
        class="form-control select2"
        required>

<option value="">-- Pilih Sub Kegiatan --</option>

@foreach($subKegiatans as $sub)

<option
value="{{ $sub->id }}"

data-program="{{ $sub->parent->parent->kode_program ?? '' }} - {{ $sub->parent->parent->nama_program ?? '' }}"

data-kegiatan="{{ $sub->parent->kode_program ?? '' }} - {{ $sub->parent->nama_program ?? '' }}"
>

{{ $sub->kode_program }} - {{ $sub->nama_program }}

</option>

@endforeach

</select>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">Program</label>

<input type="text"
id="program"
class="form-control"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">Kegiatan</label>

<input type="text"
id="kegiatan"
class="form-control"
readonly>

</div>

</div>

<div class="mb-3">

<label class="fw-bold">Indikator Kinerja</label>

<textarea name="indikator_kinerja"
class="form-control"
rows="2"
required></textarea>

</div>

<div class="row">

<div class="col-md-4">

<label class="fw-bold">Target</label>

<input type="text"
name="target"
class="form-control"
required>

</div>

<div class="col-md-4">

<label class="fw-bold">Satuan</label>

<input type="text"
name="satuan"
class="form-control"
required>

</div>

<div class="col-md-4">

<label class="fw-bold">Pagu Rencana</label>

<input type="number"
name="pagu_rencana"
class="form-control"
required>

</div>

</div>

<div class="mt-3 text-end">

<a href="{{ route('renja.index') }}" class="btn btn-secondary me-2">
Batal
</a>

<button class="btn btn-primary">
<i class="fas fa-save"></i>
Simpan
</button>

</div>

</form>

</div>

</div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>

$(document).ready(function(){

    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: '-- Pilih Data --'
    });

});

$('#sub_kegiatan').on('change', function(){

    let program = $(this).find(':selected').data('program');
    let kegiatan = $(this).find(':selected').data('kegiatan');

    $('#program').val(program);
    $('#kegiatan').val(kegiatan);

});
</script>

@endpush

@endsection
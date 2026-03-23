@extends('layouts.app')

@section('content')

<div class="container-fluid">

<h3 class="mb-4 fw-bold">
RENJA BLUD
</h3>
<a href="{{ route('renja.create') }}"
class="btn btn-primary mb-3">

Tambah RENJA

</a>
<table class="table table-bordered">

<thead>
<tr>
<th>Unit</th>
<th>Kode Sub Kegiatan</th>
<th>Sub Kegiatan</th>
<th>Target</th>
<th>Pagu</th>
<th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>

@forelse($renjas as $r)

<tr>

<td>{{ $r->unit->nama_unit ?? '-' }}</td>

<td>{{ $r->subKegiatan->kode_program ?? '-' }}</td>

<td>{{ $r->subKegiatan->nama_program ?? '-' }}</td>

<td>{{ $r->target }}</td>

<td>
Rp {{ number_format($r->pagu_rencana,0,',','.') }}
</td>
<td class="text-center">

<a href="{{ route('renja.edit',$r->id) }}"
class="btn btn-sm btn-warning">

<i class="fas fa-pencil-alt"></i>

</a>

<form action="{{ route('renja.destroy',$r->id) }}"
method="POST"
style="display:inline-block"
onsubmit="return confirm('Hapus RENJA ini?')">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">

<i class="fas fa-trash"></i>

</button>

</form>

</td>

</tr>

@empty

<tr>
<td colspan="5" class="text-center">
Belum ada data RENJA
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

@endsection
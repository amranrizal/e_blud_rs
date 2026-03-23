@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h4 class="mb-3">⚙ Parameter SBU</h4>

    <div class="card mb-4">
        <div class="card-body">

            <h5>{{ $sbu->uraian }}</h5>
            <p>
                Tarif: Rp {{ number_format($sbu->harga,0,',','.') }}
            </p>

        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">

            <h6>Daftar Parameter</h6>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Label</th>
                        <th>Tipe</th>
                        <th>Default</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sbu->sbuParameters as $param)
                    <tr>
                        <td>{{ $param->kode_parameter }}</td>
                        <td>{{ $param->label }}</td>
                        <td>{{ $param->tipe }}</td>
                        <td>{{ $param->nilai_default }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('sbu.parameter.destroy',$param->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">x</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada parameter
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <hr>

            <form method="POST"
                  action="{{ route('sbu.parameter.store',$sbu->id) }}">
                @csrf

                <div class="row g-2">
                    <div class="col-md-2">
                        <input type="text"
                               name="kode_parameter"
                               class="form-control"
                               placeholder="kode"
                               required>
                    </div>

                    <div class="col-md-3">
                        <input type="text"
                               name="label"
                               class="form-control"
                               placeholder="Label"
                               required>
                    </div>

                    <div class="col-md-2">
                        <select name="tipe"
                                class="form-select">
                            <option value="numeric">Numeric</option>
                            <option value="integer">Integer</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="number"
                               name="nilai_default"
                               class="form-control"
                               placeholder="Default">
                    </div>

                    <div class="col-md-1">
                        <input type="checkbox"
                               name="is_required"
                               checked>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-success w-100">
                            Tambah
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection

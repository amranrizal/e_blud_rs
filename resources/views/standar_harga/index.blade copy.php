@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark fw-bold mb-0">💰 Master Standar Harga (SSH/SBU)</h3>
        <div>
            <!-- Tombol Trigger Modal Import -->
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalImport">
                <i class="fas fa-file-excel"></i> Import Excel
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Tabel -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            
            <!-- Form Search Sederhana -->
            <!-- Form Filter & Search -->
            <form action="{{ route('standar-harga.index') }}" method="GET" class="mb-3">
                <div class="d-flex gap-2">
                    <!-- 1. Dropdown Tahun -->
                    <select name="tahun" class="form-select" style="width: 120px;" onchange="this.form.submit()">
                        @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                Tahun {{ $i }}
                            </option>
                        @endfor
                    </select>

                    <!-- 2. Input Search -->
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kelompok</th>
                            <th>Kode Barang</th>
                            <th>Uraian / Spesifikasi</th>
                            <th>Satuan</th>
                            <th class="text-end">Harga</th>
                            <th>Rekening Belanja (COA)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datas as $item)
                        <tr>
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->kode_kelompok }}</span>
                            </td>
                            <td>{{ $item->kode_barang ?? '-' }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->uraian }}</div>
                                <small class="text-muted">{{Str::limit($item->spesifikasi, 50)}}</small>
                            </td>
                            <td>{{ $item->satuan }}</td>
                            <td class="text-end fw-bold">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td>
                                <!-- LOGIC PINTAR: Cek Relasi -->
                                @if($item->rekening)
                                    <small class="d-block fw-bold text-primary">{{ $item->kode_akun }}</small>
                                    <span class="badge bg-light text-dark border">{{ Str::limit($item->rekening->nama_akun, 30) }}</span>
                                @else
                                    <small class="d-block text-danger">{{ $item->kode_akun }}</small>
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">⚠️ Rekening Belum Ada</span>
                                @endif
                            </td>
                            <td>
                                <form onsubmit="return confirm('Hapus item ini?');" action="{{ route('standar-harga.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                Belum ada data Standar Harga via Import.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $datas->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('standar-harga.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data SSH/SBU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel (.xlsx)</label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text">Pastikan header: kelompok, kode_barang, uraian, spesifikasi, satuan, harga, tahun, kode_rekening</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload & Proses</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
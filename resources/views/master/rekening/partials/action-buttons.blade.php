<div class="btn-group" role="group">
    
    <!-- 1. TOMBOL TAMBAH ANAK (HIJAU +) -->
    <!-- Mengarah ke Create Page dengan membawa ID Bapaknya -->
    <a href="{{ route('master.rekening.create', ['parent_id' => $row->id]) }}" 
       class="btn btn-success btn-sm" 
       title="Tambah Sub-Rekening di bawah {{ $row->nama_akun }}">
        <i class="fas fa-plus"></i>
    </a>

    <!-- 2. TOMBOL EDIT (KUNING) -->
    <!-- Mengarah ke Halaman Edit -->
    <a href="{{ route('master.rekening.edit', $row->id) }}" 
       class="btn btn-warning btn-sm" 
       title="Edit Rekening">
        <i class="fas fa-edit"></i>
    </a>

    <!-- 3. TOMBOL HAPUS (MERAH) -->
    <!-- Wajib pakai Form DELETE -->
    <form action="{{ route('master.rekening.destroy', $row->id) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Yakin ingin menghapus rekening {{ $row->kode_akun }}? Data yang dihapus tidak bisa dikembalikan.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Rekening">
            <i class="fas fa-trash"></i>
        </button>
    </form>

</div>
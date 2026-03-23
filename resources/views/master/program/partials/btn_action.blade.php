<div class="d-inline-flex">

    <!-- Edit -->
    <button type="button"
        class="btn btn-outline-warning btn-sm d-flex align-items-center justify-content-center"
        style="width:36px; height:32px;"
        onclick="editForm('{{ $row->id }}')">
        <i class="fas fa-pen"></i>
    </button>

    <!-- Delete -->
    <form action="{{ route('master.program.destroy', $row->id) }}"
        method="POST"
        onsubmit="return confirm('Yakin ingin menghapus data ini?')"
        class="ms-1">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center"
            style="width:36px; height:32px;">
            <i class="fas fa-trash"></i>
        </button>
    </form>

</div>

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program; // Pastikan Model Program di-import
use App\Http\Controllers\Controller; 
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProgramImport;


class MasterProgramController extends Controller
{
    // ❌ HAPUS FUNCTION __CONSTRUCT() YANG LAMA

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // Batasi supaya tidak bisa isi angka sembarangan
        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 10;
        }

        $programs = \App\Models\Program::orderBy('kode_program')
            ->paginate($perPage)
            ->withQueryString();

        return view('master.program.index', compact('programs', 'perPage'));
    }


    public function show($id)
    {
        try {

            $program = Program::findOrFail($id);

            return response()->json([
                'id' => $program->id,
                'kode_program' => $program->kode_program,
                'nama_program' => $program->nama_program,
                'parent_id' => $program->parent_id,
                'level' => $program->level
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);

        }
    }

public function create(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $parentId = $request->get('parent_id');
        $parent = $parentId ? Program::find($parentId) : null;
        
        return view('master.program.create', compact('parentId', 'parent'));
    }

  public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'nama_program' => 'required',
            'parent_id'    => 'nullable|exists:m_program,id'
        ]);

        $parent = null;
        $level = 'Urusan';
        $kode  = '';

        if ($request->parent_id) {

            $parent = Program::findOrFail($request->parent_id);

            $levelMap = [
                'Urusan'          => 'Bidang Urusan',
                'Bidang Urusan'   => 'Program',
                'Program'         => 'Kegiatan',
                'Kegiatan'        => 'Sub Kegiatan',
            ];

            if (!isset($levelMap[$parent->level])) {
                abort(403, 'Sub Kegiatan tidak boleh memiliki turunan.');
            }

            $level = $levelMap[$parent->level];

            // Ambil anak terakhir
            $lastChild = Program::where('parent_id', $parent->id)
                ->orderBy('kode_program', 'desc')
                ->first();

            $nextNumber = 1;

            if ($lastChild) {
                $lastNumber = intval(substr(
                    $lastChild->kode_program,
                    strrpos($lastChild->kode_program, '.') + 1
                ));
                $nextNumber = $lastNumber + 1;
            }

            // Format kode sesuai level
            $padding = match($level) {
                'Bidang Urusan', 'Program' => 2,
                'Kegiatan'                => 3,
                'Sub Kegiatan'            => 4,
                default                   => 2,
            };

            $kode = $parent->kode_program . '.' . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);

        } else {

            // ROOT URUSAN
            $last = Program::whereNull('parent_id')
                ->orderBy('kode_program', 'desc')
                ->first();

            $nextNumber = $last ? intval($last->kode_program) + 1 : 1;
            $kode = $nextNumber;
        }

        Program::create([
            'kode_program' => $kode,
            'nama_program' => $request->nama_program,
            'parent_id'    => $request->parent_id,
            'level'        => $level,
        ]);

        return redirect()
            ->route('master.program.index')
            ->with('success', 'Data berhasil disimpan');
    }


        public function edit($id)
    {
        
        // 🔒 SATPAM: Cek Admin
        if (auth()->user()->role !== 'admin') abort(403);

        // 1. Ambil data yang mau diedit
        $program = Program::findOrFail($id);

        // Cek jika data hantu (sudah dihapus tapi link masih ada)
        if (!$program) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }

        // 2. Ambil Data Induk (Parent) untuk Dropdown
        // Kita ambil semua program KECUALI dirinya sendiri (mencegah error logika bapak = anak sendiri)
        // Ini penting agar di Form Edit user bisa melihat/mengganti Induknya.
        $parents = Program::where('id', '!=', $id)->get();

        // Kirim $program (data inti) dan $parents (data dropdown) ke View
        return view('master.program.edit', compact('program', 'parents'));
    }

    public function update(Request $request, $id)
    {
        // 🔒 SATPAM: Cek Admin
        if (auth()->user()->role !== 'admin') abort(403);

        // 1. Validasi
        $request->validate([
            'kode_program' => 'required', // Sesuaikan dengan nama kolom DB
            'nama_program' => 'required',
            'parent_id' => 'nullable|exists:m_program,id', // Validasi parent_id
            // 'id_parent' => 'nullable', // Boleh kosong jika dia Level Tertinggi (Urusan)
        ]);

        // 2. Cari & Update
        $program = Program::find($id);
        
        if (!$program) {
             return back()->with('error', 'Data hilang saat mau disimpan.');
        }

        if ($program->children()->exists()) {
            if ($request->parent_id != $program->parent_id) {
                abort(403, 'Tidak boleh memindahkan data yang memiliki turunan.');
            }
        }


        // Update data
        $program->update([
            'kode_program' => $request->kode_program,
            'nama_program' => $request->nama_program,
            'parent_id' => $request->parent_id, // Pastikan nama input di form edit sama dengan ini
        ]);

        return redirect()->route('master.program.index')->with('success', 'Data berhasil diperbarui');
    }
        // --- FUNCTION HAPUS DATA (DESTROY) ---
    public function destroy($id)
    {
        // 1. Satpam: Cek Admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus data ini.');
        }

        // 2. Cari Data
        $program = Program::findOrFail($id);

        if (!$program) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // 3. CEK INTEGRITAS: Jangan hapus jika masih punya anak!
        // Contoh: Program tidak boleh dihapus jika di dalamnya masih ada Kegiatan.
        // Kita hitung jumlah anaknya.
        if ($program->children()->count() > 0) {
            return back()->with('error', 'GAGAL HAPUS: Data ini masih memiliki Sub/Turunan di bawahnya. Hapus dulu anak-anaknya.');
        }

        // 4. Proses Hapus
        $program->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function import(Request $request)
    {
         
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new ProgramImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data Nomenklatur berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

   public function search(Request $request)
    {
        $search = trim($request->get('q'));

        if (!$search) {
            return response()->json([]);
        }

        $searchNoDot = str_replace('.', '', $search);

        $query = \App\Models\Program::query();

        $query->where(function ($q) use ($search, $searchNoDot) {

            // full like search
            $q->where('kode_program', 'like', "%{$search}%")
            ->orWhere('nama_program', 'like', "%{$search}%")

            // search tanpa titik
            ->orWhereRaw("REPLACE(kode_program,'.','') LIKE ?", ["%{$searchNoDot}%"])

            // hierarchy prefix search
            ->orWhere('kode_program', 'like', "{$search}%");

        });

        // jika user mengetik X.XX tampilkan juga root X
        if (str_starts_with(strtoupper($search), 'X.XX')) {
            $query->orWhere('kode_program', 'X');
        }

        $data = $query
            ->orderByRaw("
                CASE
                    WHEN kode_program = 'X' THEN 0
                    WHEN kode_program = 'X.XX' THEN 1
                    ELSE 2
                END
            ")
            ->orderByRaw("LENGTH(kode_program)")
            ->orderBy('kode_program')
            ->limit(20)
            ->get();

        return response()->json(
            $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->kode_program . ' - ' . $item->nama_program,
                ];
            })
        );
    }

    public function goto(Request $request)
    {
        $id = $request->kode; // sebenarnya ini id

        $perPage = $request->get('per_page', 10);

        // cari posisi berdasarkan ID
        $position = \App\Models\Program::orderBy('kode_program')
            ->pluck('id')
            ->search($id);

        if ($position === false) {
            return redirect()->route('master.program.index');
        }

        $page = floor($position / $perPage) + 1;

        return redirect()->route('master.program.index', [
            'page' => $page,
            'selected_id' => $id,
            'per_page' => $perPage
        ]);
    }
}
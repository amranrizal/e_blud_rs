<?php

namespace App\Http\Controllers;

use App\Models\StandarHarga;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StandarHargaImport;
use App\Models\SbuParameter;
use App\Models\Rekening;

class StandarHargaController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $search = $request->search;
        $perPage = $request->get('perPage', 10);
        $rekeningList = Rekening::where('is_active', 1)
            ->whereIn('level', ['Objek','Rincian'])
            ->orderBy('kode_akun')
            ->get();
        $query = StandarHarga::where('tahun', $tahun);
        

        // 🔍 FILTER SEARCH
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                ->orWhere('uraian', 'like', "%{$search}%")
                ->orWhere('spesifikasi', 'like', "%{$search}%")
                ->orWhere('kode_rekening', 'like', "%{$search}%");
            });
        }

        // ORDER TETAP
        $query->orderByRaw('COALESCE(parent_id, id)')
            ->orderBy('is_group','desc')
            ->orderBy('kode_barang');

        // PAGINATION
        $datas = $query->paginate($perPage)->withQueryString();

        //Dropdown Group
        $groups = StandarHarga::where('tahun', $tahun)
            ->whereNull('parent_id') // hanya GROUP
            ->orderBy('kode_barang')
            ->get();

        return view('standar_harga.index', compact(
            'datas',
            'tahun',
            'rekeningList',
            'groups'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'tahun' => 'required|integer'
        ]);

        try {

            Excel::import(
                new StandarHargaImport($request->tahun),
                $request->file('file')
            );

            return redirect()
                ->route('standar-harga.index')
                ->with('success','Import SSH berhasil');

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Import gagal : '.$e->getMessage()
            );
        }
    }

    public function destroy($id)
    {
        $ssh = StandarHarga::findOrFail($id);

        if ($ssh->is_locked_year) {
            return back()->with('error', 'SSH tahun ini sudah dikunci dan tidak bisa dihapus.');
        }

        if ($ssh->is_locked) {
            return back()->with('error', 'SSH tidak bisa dihapus karena sudah dipakai HSPK.');
        }

        $ssh->delete();

        return back()->with('success', 'SSH berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $ssh = StandarHarga::findOrFail($id);

        if ($ssh->is_locked_year) {
            return back()->with('error', 'SSH tahun ini sudah dikunci dan tidak bisa diedit.');
        }

        $request->validate([
            'kode_kelompok' => 'required|in:SSH,SBU',
            'uraian'       => 'required|string',
            'spesifikasi'  => 'nullable|string',
            'satuan'       => 'required|string',
            'harga'        => 'required|numeric|min:0',
        ]);

        if (preg_match('/paket|kegiatan|pekerjaan|pengadaan|pembangunan/i', $request->uraian)) {
            return back()->with('error', 'Nama mengandung kata yang bukan SSH.');
        }

        if ($ssh->is_locked) {
            return back()->with('error', 'SSH sudah terkunci karena dipakai HSPK.');
        }

        $ssh->update([
            'kode_kelompok' => $request->kode_kelompok,
            'uraian'       => $request->uraian,
            'spesifikasi'  => $request->spesifikasi,
            'satuan'       => $request->satuan,
            'harga'        => $request->harga,
        ]);

        return back()->with('success', 'SSH berhasil diperbarui');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kelompok' => 'required|in:SSH,SBU',
            'uraian'       => 'required|string',
            'spesifikasi'  => 'nullable|string',
            'satuan'       => 'required|string',
            'harga'        => 'required|numeric|min:0',
            'tahun'        => 'required|digits:4',
        ]);

        if (preg_match('/paket|kegiatan|pekerjaan|pengadaan|pembangunan/i', $request->uraian)) {
            return back()->with('error', 'Nama mengandung kata yang bukan SSH.');
        }

        // ambil parent (group)
        $parent = StandarHarga::findOrFail($request->parent_id);
        $uraianFinal = $parent->uraian;

        if ($request->spesifikasi) {
            $uraianFinal .= ' - ' . $request->spesifikasi;
        }

        // ambil kode parent
        $parentKode = $parent->kode_barang;

        // cari child terakhir
        $lastChild = StandarHarga::where('parent_id', $parent->id)
            ->orderByDesc('kode_barang')
            ->first();

        // tentukan nomor urut
        if ($lastChild) {
            $lastNumber = (int) substr($lastChild->kode_barang, -5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        // gabungkan
        $kodeBarang = $parentKode . '.' . $newNumber;

        // 🔥 STEP 5 (ANTISIPASI DUPLIKAT)
        while (StandarHarga::where('kode_barang', $kodeBarang)->exists()) {
            $lastNumber++;
            $newNumber = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);
            $kodeBarang = $parentKode . '.' . $newNumber;
        }
        $exists = StandarHarga::where('uraian', $request->uraian)
            ->where('spesifikasi', $request->spesifikasi)
            ->where('satuan', $request->satuan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Data SSH sudah ada (duplikat)');
        }

        StandarHarga::create([
            'parent_id'      => $request->parent_id,
            'kode_barang'   => $kodeBarang,
            'kode_kelompok' => $request->kode_kelompok,
            'kode_rekening' => $request->kode_rekening 
                ? implode(',', $request->kode_rekening) 
                : null,
            'uraian'        => $uraianFinal,
            'spesifikasi'   => $request->spesifikasi,
            'satuan'        => $request->satuan,
            'harga'         => $request->harga,
            'tahun'         => $request->tahun,
        ]);

        return back()->with('success', 'SSH berhasil ditambahkan');
    }

    public function readonly(Request $request)
    {
        $query = StandarHarga::query();

        if ($request->filled('q')) {

            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('kode_barang', 'like', "%{$q}%")
                    ->orWhere('uraian', 'like', "%{$q}%")
                    ->orWhere('spesifikasi', 'like', "%{$q}%")
                    ->orWhere('kode_kelompok', 'like', "%{$q}%");
            });
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $data = $query
            ->orderBy('kode_kelompok')
            ->orderBy('uraian')
            ->paginate(15)
            ->withQueryString();

        return view('standar_harga.readonly', compact('data'));
    }

    public function showSbu($id)
    {
        $sbu = StandarHarga::with('sbuParameters')
            ->where('kode_kelompok', 'SBU')
            ->findOrFail($id);

        return view('standar_harga.sbu-detail', compact('sbu'));
    }

    public function tambahParameter(Request $request, $id)
    {
        $request->validate([
            'kode_parameter' => 'required|string',
            'label' => 'required|string',
            'tipe' => 'required|in:numeric,integer',
            'nilai_default' => 'nullable|numeric',
        ]);

        SbuParameter::create([
            'standar_harga_id' => $id,
            'kode_parameter' => $request->kode_parameter,
            'label' => $request->label,
            'tipe' => $request->tipe,
            'nilai_default' => $request->nilai_default,
            'is_required' => $request->has('is_required'),
            'urutan' => 1,
        ]);

        return back()->with('success', 'Parameter ditambahkan.');
    }

    public function hapusParameter($id)
    {
        $param = SbuParameter::findOrFail($id);
        $param->delete();

        return back()->with('success', 'Parameter dihapus.');
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $data = Rekening::where('is_active', 1)
            ->where(function($q2) use ($search) {
                $q2->where('kode_akun', 'like', "%{$search}%")
                ->orWhere('nama_akun', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json(
            $data->map(function($item){
                return [
                    'id' => $item->kode_akun,
                    'text' => $item->kode_akun . ' - ' . $item->nama_akun
                ];
            })
        );
    }
}
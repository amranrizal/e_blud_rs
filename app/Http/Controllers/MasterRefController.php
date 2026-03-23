<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program; // <--- PAKAI MODEL INI
use App\Models\Rekening; 

class MasterRefController extends Controller
{
    public function index(Request $request)
    {
        $level = $request->level ?? 'bidang_urusan';
        $data = [];
        $title = '';

        // KITA PAKAI MODEL 'Program' TAPI KITA FILTER LEVELNYA
        switch ($level) {
            case 'bidang_urusan':
                // Level 2 (Bidang), Parentnya (Level 1/Urusan)
                $data = Program::where('level', 2)
                        ->with('parent') 
                        ->get();
                $title = 'Referensi: Bidang Urusan';
                break;

            case 'program':
                // Level 3 (Program), Parent (Bidang), Parent.Parent (Urusan)
                $data = Program::where('level', 3)
                        ->with('parent.parent') 
                        ->get();
                $title = 'Referensi: Program';
                break;

            case 'kegiatan':
                // Level 4 (Kegiatan) -> tarik 3 tingkat ke atas
                $data = Program::where('level', 4)
                        ->with('parent.parent.parent') 
                        ->get();
                $title = 'Referensi: Kegiatan';
                break;

            case 'sub_kegiatan':
                // Level 5 (Sub Kegiatan) -> tarik 4 tingkat ke atas
                $data = Program::where('level', 5)
                        ->with('parent.parent.parent.parent') 
                        ->get();
                $title = 'Referensi: Sub Kegiatan';
                break;
            
            case 'akun': // Parameter URL tetap 'akun' biar konsisten dengan sidebar
                        // Ambil Level 6 (Sub Rincian Objek)
                        // Kita load parent-nya (Level 5) buat jadi Judul Card
                $data = Rekening::with('parent')
                        ->orderBy('kode_akun', 'asc')
                        ->get();
            
        $title = 'Referensi: Seluruh Kode Rekening (Level 1 - 6)';
        break;    

            default:
                abort(404);
        }

        return view('master.ref.index', compact('data', 'level', 'title'));
    }
}
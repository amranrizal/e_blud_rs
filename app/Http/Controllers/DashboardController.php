<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// --- IMPORT MODEL YANG DIBUTUHKAN ---
use App\Models\Program;
use App\Models\Rekening;
use App\Models\User;
// Cek nama file model unit anda, apakah Unit.php atau UnitKerja.php?
// Jika error, ganti baris bawah ini jadi: use App\Models\Unit;
use App\Models\UnitKerja; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // --- 1. LOGIKA LAMA (Dari web.php dipindah kesini) ---
        // Kita pakai nama variabel yang SAMA PERSIS dengan di web.php dulu
        // Supaya view dashboard.blade.php tidak kaget/error.
        
        $total_program  = Program::count();
        
        // Cek dulu apakah tabel rekening ada, kalau belum ada kasih 0 biar gak error
        try {
            $total_rekening = Rekening::count();
        } catch (\Exception $e) {
            $total_rekening = 0;
        }

        // Cek Model Unit (Sesuaikan dengan nama Model Unit Bossku)
        // Kalau nama modelnya Unit.php, ganti jadi \App\Models\Unit::count();
        try {
            $total_unit = \App\Models\UnitKerja::count(); 
        } catch (\Exception $e) {
             // Fallback kalau nama modelnya ternyata Unit (bukan UnitKerja)
            try {
                $total_unit = \App\Models\Unit::count();
            } catch (\Exception $x) {
                $total_unit = 0;
            }
        }


        // --- 2. LOGIKA BARU (Untuk Card User - Khusus Admin) ---
        $jumlahUser = 0;
        if ($user && $user->role === 'admin') {
            $jumlahUser = User::count();
        }

        // --- 3. KIRIM SEMUA KE VIEW ---
        return view('dashboard', [
            'total_program'  => $total_program,
            'total_rekening' => $total_rekening,
            'total_unit'     => $total_unit,
            'jumlahUser'     => $jumlahUser
        ]);
    }
}
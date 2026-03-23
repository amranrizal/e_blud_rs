<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller; 

class UserController extends Controller
{
    // ❌ TIDAK ADA __CONSTRUCT DI SINI

    // 1. INDEX (DAFTAR USER)
    public function index()
    {
        // Satpam: Cek Admin
        if (auth()->user()->role !== 'admin') abort(403);

        $users = User::all();
        return view('users.index', compact('users'));
    }

    // 2. CREATE (FORM TAMBAH)
    public function create()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        
        // Ambil data unit kerja untuk dropdown (jika user adalah pelaksana)
        $units = \App\Models\UnitKerja::all(); 
        return view('users.create', compact('units'));
    }

    // 3. STORE (SIMPAN DATA)
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,pimpinan,verifikator,user',
            'unit_id'  => 'nullable|exists:m_unit_kerja,id',
        ]);

        // RULE UNIT
        if (in_array($request->role, ['user','verifikator']) && empty($request->unit_id)) {
            return back()
                ->withInput()
                ->withErrors(['unit_id' => 'Role ini wajib memilih Unit Kerja']);
        }

        if (in_array($request->role, ['admin','pimpinan'])) {
            $request->merge(['unit_id' => null]);
        }

        $user = new User();
        $user->name      = $request->name;
        $user->email     = $request->email;
        $user->password  = Hash::make($request->password);
        $user->role      = $request->role;
        $user->unit_id   = $request->unit_id;

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }


    // 4. EDIT (FORM EDIT)
    public function edit($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $user = User::find($id);
        $units = \App\Models\UnitKerja::all();
        
        return view('users.edit', compact('user', 'units'));
    }

    // 5. UPDATE (SIMPAN PERUBAHAN)
    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|in:admin,pimpinan,verifikator,user',
            'unit_id'=> 'nullable|exists:m_unit_kerja,id',
        ]);

        // RULE UNIT
        if (in_array($request->role, ['user','verifikator']) && empty($request->unit_id)) {
            return back()
                ->withInput()
                ->withErrors(['unit_id' => 'Role ini wajib memilih Unit Kerja']);
        }

        if (in_array($request->role, ['admin','pimpinan'])) {
            $request->merge(['unit_id' => null]);
        }

        $user->name      = $request->name;
        $user->email     = $request->email;
        $user->role      = $request->role;
        $user->unit_id   = $request->unit_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'Data User diperbarui');
    }


    // 6. DESTROY (HAPUS)
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        // Mencegah Admin menghapus dirinya sendiri
        if (auth()->id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        $user = User::find($id);
        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}
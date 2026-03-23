<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use Illuminate\Http\Request;

class AdminOverrideController extends Controller
{

    /**
     * =========================================================
     * FORCE STATUS (ADMIN OVERRIDE)
     * =========================================================
     */
    public function forceStatus(Request $request, Anggaran $anggaran)
    {
        $this->authorize('force', $anggaran);

        $request->validate([
            'status' => 'required|string',
            'reason' => 'required|string|min:10',
        ]);

        $this->workflow->adminForceTransition(
            $anggaran,
            $request->status,
            $request->reason
        );

        return back()->with('warning', 'ADMIN OVERRIDE berhasil');
    }

}

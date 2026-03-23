<?php

namespace App\Policies;

use App\Models\Anggaran;
use App\Models\User;

class AnggaranPolicy
{
    public function view(User $user, Anggaran $anggaran): bool
    {
        // admin dan pimpinan bebas
        if (in_array($user->role, ['admin', 'pimpinan'])) {
            return true;
        }

        // pastikan relasi ada dulu
        if (! $anggaran->relationLoaded('unitKerja')) {
            $anggaran->load('unitKerja');
        }

        return $user->unit_id === $anggaran->unitKerja?->unit_id;
    }

    public function submit(User $user, Anggaran $anggaran): bool
    {
        return in_array(strtolower($user->role), ['User', 'Admin'])
            && in_array($anggaran->status, ['Draft', 'Ditolak']);
    }

    public function update(User $user, Anggaran $anggaran): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        // User hanya boleh edit saat draft
        if ($user->role === 'user') {
            return $anggaran->status === 'Draft'
                && $user->unit_id === $anggaran->unitKerja?->unit_id;
        }

        // Pimpinan tidak boleh edit
        return false;
    }

    public function approve(User $user, Anggaran $anggaran): bool
    {
        return in_array($user->role, ['pimpinan', 'admin'])
            && $anggaran->status === 'Divalidasi';
    }

    public function validate(User $user, Anggaran $anggaran): bool
    {
        return in_array($user->role, ['verifikator', 'admin'])
            && $anggaran->status === 'Diajukan';
    }
}

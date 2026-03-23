<?php

namespace App\Policies;

use App\Models\PaguIndikatif;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaguIndikatifPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaguIndikatif $paguIndikatif): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaguIndikatif $paguIndikatif): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaguIndikatif $paguIndikatif): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaguIndikatif $paguIndikatif): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaguIndikatif $paguIndikatif): bool
    {
        return false;
    }

    public function approve(User $user, PaguIndikatif $pagu): bool
    {
        return in_array($user->role, ['pimpinan', 'admin'])
            && $pagu->status_validasi === 'submitted';
    }

}

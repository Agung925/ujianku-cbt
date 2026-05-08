<?php

namespace App\Policies;

use App\Models\Soal;
use App\Models\User;

class SoalPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Soal $soal): bool
    {
        // Guru hanya bisa lihat soal milik mereka
        return $user->guru?->id === $soal->guru_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Soal $soal): bool
    {
        // Guru hanya bisa edit soal milik mereka
        return $user->guru?->id === $soal->guru_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Soal $soal): bool
    {
        // Guru hanya bisa hapus soal milik mereka
        return $user->guru?->id === $soal->guru_id;
    }
}

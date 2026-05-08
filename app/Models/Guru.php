<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Models\Tenant;

class Guru extends Model
{
    use BelongsToTenant;
    /**
     * Model Guru untuk menyimpan data guru/pendidik
     * Berguna untuk tenant scoping dan management user guru
     */
    
    protected $fillable = [
        'tenant_id',
        'user_id',
        'email',
        'nama',
        'nip',
        'foto_profil',
        'is_wali_kelas',
        'is_active',
    ];

    protected $casts = [
        'is_wali_kelas' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    /**
     * Guru belongs to Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    /**
     * Guru may have User account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Guru has many Soal (questions)
     */
    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class);
    }

    /**
     * Guru has many Ujian (exams)
     */
    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }
}

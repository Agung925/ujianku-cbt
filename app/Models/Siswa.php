<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Models\Tenant;

class Siswa extends Model
{
    /**
     * Model Siswa untuk menyimpan data pelajar
     * Menggunakan SoftDeletes untuk keep data history
     */
    
    use BelongsToTenant, SoftDeletes, HasFactory;

    protected $fillable = [
        'tenant_id',
        'nis',
        'nama',
        'email',
        'password',
        'foto',
        'kelas',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    /**
     * Siswa belongs to Tenant
     */
    public function tenant()
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    /**
     * Siswa has many JawabanSiswa (answers)
     */
    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    /**
     * Siswa has many Nilai (grades)
     */
    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }
}

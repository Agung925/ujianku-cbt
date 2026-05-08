<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Models\Tenant;

class KategoriUjian extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'nama',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    public function tenant(): BelongsTo
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class);
    }

    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }
}

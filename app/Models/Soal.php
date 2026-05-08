<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Models\Tenant;

class Soal extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'kategori_ujian_id',
        'guru_id',
        'pertanyaan',
        'tipe_soal',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'kunci_jawaban',
        'bobot',
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

    public function kategoriUjian(): BelongsTo
    {
        return $this->belongsTo(KategoriUjian::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class);
    }
}

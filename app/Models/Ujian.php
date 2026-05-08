<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Models\Tenant;

class Ujian extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'guru_id',
        'kategori_ujian_id',
        'judul',
        'deskripsi',
        'tgl_mulai',
        'tgl_selesai',
        'waktu_durasi',
        'is_acak_soal',
        'is_acak_opsi',
        'is_active',
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
        'is_acak_soal' => 'boolean',
        'is_acak_opsi' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    public function tenant(): BelongsTo
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function kategoriUjian(): BelongsTo
    {
        return $this->belongsTo(KategoriUjian::class);
    }

    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    public function nilai(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function soal(): BelongsToMany
    {
        return $this->belongsToMany(Soal::class, 'exam_questions', 'ujian_id', 'soal_id')
                    ->withPivot('urutan')
                    ->orderByPivot('urutan');
    }
}

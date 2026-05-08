<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Models\Tenant;

class JawabanSiswa extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'ujian_id',
        'siswa_id',
        'soal_id',
        'jawaban',
        'waktu_mulai',
        'waktu_selesai',
        'is_submitted',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_submitted' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    public function tenant(): BelongsTo
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }
}

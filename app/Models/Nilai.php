<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Models\Tenant;

class Nilai extends Model
{
    use BelongsToTenant, HasFactory;
    protected $fillable = [
        'tenant_id',
        'ujian_id',
        'siswa_id',
        'nilai_otomatis',
        'nilai_essay',
        'nilai_akhir',
        'status',
        'catatan_guru',
    ];

    protected $casts = [
        'nilai_otomatis' => 'float',
        'nilai_essay' => 'float',
        'nilai_akhir' => 'float',
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

    // ===== ACCESSORS/MUTATORS =====
    /**
     * Compute nilai_akhir otomatis dari nilai_otomatis dan nilai_essay
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->nilai_otomatis !== null && $model->nilai_essay !== null) {
                $model->nilai_akhir = ($model->nilai_otomatis + $model->nilai_essay) / 2;
            } elseif ($model->nilai_otomatis !== null) {
                $model->nilai_akhir = $model->nilai_otomatis;
            } elseif ($model->nilai_essay !== null) {
                $model->nilai_akhir = $model->nilai_essay;
            }
        });
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Models\Tenant;

class FileUpload extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'size',
        'uploadable_type',
        'uploadable_id',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====
    public function tenant(): BelongsTo
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    /**
     * Polymorphic relationship
     * Can belong to Guru, Siswa, Soal, etc
     */
    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

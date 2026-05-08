<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Models\Tenant;

class LogoIdentitas extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'logo_identitas';

    protected $fillable = [
        'tenant_id',
        'nama_file',
        'path',
        'file_type',
        'mime_type',
        'size',
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

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

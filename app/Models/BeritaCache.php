<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Models\Tenant;

class BeritaCache extends Model
{
    use BelongsToTenant;
    protected $table = 'berita_caches';

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'source',
        'url',
        'image_url',
        'published_at',
        'cached_at',
        'expires_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'cached_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====
    public function tenant(): BelongsTo
    {
        return $this->belongsTo('Stancl\Tenancy\Models\Tenant', 'tenant_id');
    }

    // ===== SCOPES =====
    /**
     * Scope: Get only non-expired news
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now())
                     ->orWhereNull('expires_at');
    }
}

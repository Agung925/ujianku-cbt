<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;

/**
 * Trait BelongsToTenant
 * 
 * Automatic tenant scoping untuk semua tenant-scoped models.
 * Trait ini memastikan bahwa setiap query automatically filtered by current tenant.
 * 
 * Usage di model:
 *   use BelongsToTenant;
 * 
 * Setiap model akan automatically:
 * - Memiliki tenant() relationship
 * - Filtered by current tenant dalam semua queries (via global scope)
 * - Dapat create with tenant via createForTenant() method
 * 
 * @method static \Illuminate\Database\Eloquent\Builder addGlobalScope(string $name, \Closure|ScopeInterface $scope)
 * @method static self create(array $attributes = [])
 * @method static self updateOrCreate(array $attributes, array $values = [])
 */
trait BelongsToTenant
{
    /**
     * Boot the trait
     * Attach global scope untuk automatic tenant filtering
     */
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope('tenant', new TenantScope());
    }

    /**
     * Define the tenant relationship
     * Setiap model yang use trait ini has one tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            'Stancl\Tenancy\Models\Tenant',
            'tenant_id',
            'id'
        );
    }

    /**
     * Get current tenant ID from context (usually dari request)
     * Menggunakan stancl/tenancy untuk get current tenant
     */
    public static function getCurrentTenantId(): ?string
    {
        try {
            return tenancy()->tenant?->id;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create model for current tenant
     * 
     * Usage:
     *   Guru::createForTenant([
     *       'email' => 'guru@test.com',
     *       'nama' => 'Pak Budi',
     *   ]);
     */
    public static function createForTenant(array $attributes = [])
    {
        $tenantId = static::getCurrentTenantId();
        
        if (!$tenantId) {
            throw new \Exception('No active tenant context');
        }

        return static::create(array_merge($attributes, [
            'tenant_id' => $tenantId,
        ]));
    }

    /**
     * Create or update model for current tenant
     * 
     * Usage:
     *   Guru::updateOrCreateForTenant(['email' => 'guru@test.com'], ['nama' => 'Pak Budi']);
     */
    public static function updateOrCreateForTenant(
        array $attributes,
        array $values = []
    ) {
        $tenantId = static::getCurrentTenantId();
        
        if (!$tenantId) {
            throw new \Exception('No active tenant context');
        }

        return static::updateOrCreate(
            array_merge($attributes, ['tenant_id' => $tenantId]),
            $values
        );
    }
}

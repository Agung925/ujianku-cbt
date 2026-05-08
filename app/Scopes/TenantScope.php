<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class TenantScope
 * 
 * Global scope yang automatically filter queries by current tenant.
 * Digunakan oleh BelongsToTenant trait untuk ensure tenant isolation.
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * 
     * Setiap query otomatis di-scope ke current tenant via WHERE clause.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get current tenant ID
        $tenantId = $this->getCurrentTenantId();

        // Jika ada active tenant, apply WHERE filter
        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', '=', $tenantId);
        }
    }

    /**
     * Get current tenant ID dari Tenancy context
     */
    private function getCurrentTenantId(): ?string
    {
        try {
            return \Stancl\Tenancy\Facades\Tenancy::getTenant()?->id;
        } catch (\Exception $e) {
            // No active tenant context
            return null;
        }
    }
}

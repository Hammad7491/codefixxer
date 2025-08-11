<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-set tenant_id on create
        static::creating(function (Model $model) {
            if (empty($model->tenant_id) && ($tenantId = static::resolveTenantId())) {
                $model->tenant_id = $tenantId;
            }
        });

        // Global scope: limit all queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = static::resolveTenantId()) {
                $builder->where($builder->qualifyColumn('tenant_id'), $tenantId);
            }
        });
    }

    protected static function resolveTenantId(): ?int
    {
        $user = Auth::user(); // assumes users table has tenant_id
        return $user?->tenant_id ?? null;
    }

    // Optional: manually target a tenant and bypass global scope
    public function scopeForTenant(Builder $query, ?int $tenantId = null): Builder
    {
        $tenantId = $tenantId ?? static::resolveTenantId();

        return $query->withoutGlobalScope('tenant')
                     ->where($query->qualifyColumn('tenant_id'), $tenantId);
    }
}

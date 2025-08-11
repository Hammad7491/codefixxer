<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'tenant_id', 'milestone_id', 'name', 'description', 'status',
    ];

    // Relations
    public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'milestone_id');
    }

    // Scope
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

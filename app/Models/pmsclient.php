<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsClient extends Model
{
    protected $table = 'pms_clients';

    protected $fillable = [
        'tenant_id', 'project_id', 'name', 'email', 'phone',
    ];

    // Relations
    public function project()
    {
        return $this->belongsTo(PmsProject::class, 'project_id');
    }

    // Scope
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

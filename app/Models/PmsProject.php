<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsProject extends Model
{
    protected $table = 'pms_projects';

    protected $fillable = [
        'tenant_id', 'name', 'description', 'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    // Relations
    public function client()
    {
        return $this->hasOne(PmsClient::class, 'project_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'project_id');
    }

    // Simple scope
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

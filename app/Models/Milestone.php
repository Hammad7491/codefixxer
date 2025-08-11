<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $table = 'pms_milestone';

    protected $fillable = [
        'tenant_id', 'project_id', 'title', 'description', 'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relations
    public function project()
    {
        return $this->belongsTo(PmsProject::class, 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'milestone_id');
    }

    // Scope
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

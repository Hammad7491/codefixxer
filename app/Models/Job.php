<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'cv_jobs'; // 👈 use cv_jobs, not jobs

    protected $fillable = [
        'tenant_id',
        'title',
        'organization_name',
        'employment_type',
        'start_date',
        'end_date',
        'location',
        'tools_used',
    ];

    protected $casts = [
        'tenant_id'  => 'integer',
        'start_date' => 'date',
        'end_date'   => 'date',
        'tools_used' => 'array',
    ];
}

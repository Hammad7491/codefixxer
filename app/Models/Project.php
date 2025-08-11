<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'cv_projects';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'client',
        'duration_weeks',
        'live_link',
        'description',
        'video_path',
        'documentation_path',
        'images',
        'tools_used',
    ];

    protected $casts = [
        'tenant_id'      => 'integer',
        'duration_weeks' => 'integer',
        'images'         => 'array',
        'tools_used'     => 'array',
    ];
}

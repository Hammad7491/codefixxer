<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'category',
        'experience_years',
        'tools',
        'notes',
    ];

    protected $casts = [
        'tenant_id'        => 'integer',   // ← add this
        'experience_years' => 'integer',
        'tools'            => 'array',
    ];
}

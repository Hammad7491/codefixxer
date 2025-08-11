<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = [
        'tenant_id',
        'degree_name',
        'institute_name',
        'start_date',
        'end_date',
        'field_of_study',
        'grade_gpa',
        'location',
        'certifications',   // json
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'certifications' => 'array',
    ];
}

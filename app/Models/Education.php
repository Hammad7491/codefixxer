<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations';   // <— add this if you had the wrong name

    protected $fillable = [
        'tenant_id','degree_name','institute_name','start_date','end_date',
        'field_of_study','grade_gpa','location','certifications',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'certifications' => 'array',
    ];
}

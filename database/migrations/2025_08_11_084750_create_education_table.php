<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');               // multi-tenant scope

            $table->string('degree_name');                         // Matric, BS, MS, etc.
            $table->string('institute_name');
            $table->date('start_date');
            $table->date('end_date')->nullable();                  // null => currently enrolled
            $table->string('field_of_study');
            $table->string('grade_gpa')->nullable();               // "3.7 / A" etc.
            $table->string('location')->nullable();                // City, Country
            $table->json('certifications')->nullable();            // [] as JSON

            $table->timestamps();

            $table->index('tenant_id');
            // If you have tenants table:
            // $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};

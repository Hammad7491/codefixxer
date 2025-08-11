<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cv_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');             // multi-tenant
            $table->string('name');                               // Project Name
            $table->string('type');                               // Project Type
            $table->string('client');                             // Client/Organization
            $table->unsignedInteger('duration_weeks');            // Duration (weeks)
            $table->string('live_link')->nullable();              // Live URL
            $table->text('description')->nullable();              // Description
            $table->string('video_path')->nullable();             // Stored video path
            $table->string('documentation_path')->nullable();     // Stored doc path (zip/pdf/rar)
            $table->json('images')->nullable();                   // Array of image paths
            $table->json('tools_used')->nullable();               // Array of tools/tech
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_projects');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();

            // Basic project info
            $table->string('name');
            $table->string('project_type');    // e.g. New Build, Renovation, Remodel
            $table->enum('status', ['To Start','In Progress','Paused','Completed'])
                  ->default('To Start');

            // Client info
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();

            // Location & dates
            $table->text('address');
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Who’s running it
            $table->foreignId('site_manager_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
        });

        // Pivot table for site <> collaborators (many-to-many)
        Schema::create('site_collaborator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')
                  ->constrained('sites')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });

        // Photos
        Schema::create('site_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')
                  ->constrained('sites')
                  ->onDelete('cascade');
            $table->string('path');      // stored in storage/app/public/site_photos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_photos');
        Schema::dropIfExists('site_collaborator');
        Schema::dropIfExists('sites');
    }
};

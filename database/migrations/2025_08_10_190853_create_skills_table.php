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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id'); // Tenant ID for multi-tenant support
            $table->string('first_name');
            $table->string('last_name');
            $table->string('category');
            $table->integer('experience_years');
            $table->json('tools')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Optional: add foreign key constraint if tenants table exists
            // $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};

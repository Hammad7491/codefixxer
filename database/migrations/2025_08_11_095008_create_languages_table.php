<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');           // multi-tenant scope
            $table->string('name');                            // e.g. English, Urdu
            $table->string('proficiency');                     // Beginner, Intermediate, Advanced, Fluent, Native
            $table->timestamps();

            // Avoid duplicates per tenant (same language once)
            $table->unique(['tenant_id', 'name']);
            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_milestones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id');              // multi-tenant scope
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();

            $table->string('name');
            $table->date('due_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);

            $table->timestamps();

            $table->index(['tenant_id', 'invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_milestones');
    }
};

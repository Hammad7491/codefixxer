<?php
// database/migrations/2025_08_11_000000_create_contracts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');

            $table->string('contract_number')->nullable();   // e.g. #1001
            $table->string('title')->nullable();
            $table->text('purpose')->nullable();

            // Client info
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->text('client_address')->nullable();

            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Section 2
            $table->text('project_timeline')->nullable();
            $table->text('payment_terms')->nullable();

            // Section 3
            $table->text('revisions')->nullable();
            $table->text('ownership_ip')->nullable();
            $table->text('confidentiality')->nullable();
            $table->text('client_responsibilities')->nullable();
            $table->text('termination_clause')->nullable();

            // Section 4
            $table->text('dispute_resolution')->nullable();
            $table->text('limitation_of_liability')->nullable();
            $table->text('amendments')->nullable();

            // Totals
            $table->decimal('total_cost', 12, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'contract_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

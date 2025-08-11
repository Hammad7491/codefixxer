<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id');               // multi-tenant scope
            $table->string('invoice_number')->nullable();          // e.g. #1276 (unique per tenant)
            $table->string('client_name');
            $table->string('contact_person')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->text('client_address')->nullable();

            $table->string('project_title')->nullable();
            $table->text('project_description')->nullable();

            // pricing meta
            $table->string('discount_name')->nullable();
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('discount_value', 10, 2)->default(0);   // % or fixed amount
            $table->decimal('tax_percent', 5, 2)->default(0);       // 0 - 100

            // payment info
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('account_holder')->nullable();
            $table->text('terms')->nullable();

            // calculated
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'created_at']);
            $table->unique(['tenant_id', 'invoice_number']);       // avoid dup numbers per tenant
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

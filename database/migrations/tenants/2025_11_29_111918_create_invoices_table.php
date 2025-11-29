<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Multitenancy\Models\Tenant;

return new class extends Migration
{
    public function up(): void
    {
        $tenant = Tenant::current();

        // IMPORTANT: ensure we have an active tenant
        if (! $tenant) {
            throw new Exception("No tenant context is active while running tenant migration.");
        }

        Schema::create('invoices', function (Blueprint $table) use ($tenant) {

            $table->id();

            // SINGLE TENANCY → add tenant_id column
            if ($tenant->tenancy_type === 'single') {
                $table->unsignedBigInteger('tenant_id')->index();
            }

            $table->string('invoice_number');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

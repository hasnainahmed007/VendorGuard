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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->string('provider', 20)->default('manual');
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->string('verified_phone', 20)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->char('current_bank_last4', 4)->nullable();
            $table->string('current_routing_hash', 64)->nullable();
            $table->unsignedInteger('risk_score')->default(0);
            $table->boolean('payment_hold')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'provider', 'external_id']);
            $table->index('tenant_id');
            $table->index(['tenant_id', 'payment_hold']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};

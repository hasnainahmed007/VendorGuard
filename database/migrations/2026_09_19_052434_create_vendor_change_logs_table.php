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
        Schema::create('vendor_change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('field_changed', 50);
            $table->string('old_value_hash', 64)->nullable();
            $table->string('new_value_hash', 64)->nullable();
            $table->string('source', 20);
            $table->string('raw_source_ref')->nullable();
            $table->timestamp('detected_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'vendor_id', 'raw_source_ref']);
            $table->index(['tenant_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_change_logs');
    }
};

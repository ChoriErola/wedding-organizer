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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', [
                'unpaid',
                'pending',
                'approved',
                'rejected',
            ])->default('unpaid')->after('status');

            $table->timestamp('payment_approved_at')->nullable();
            $table->foreignId('payment_approved_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('payment_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', [
                'unpaid',
                'pending',
                'approved',
                'rejected',
            ])->default('unpaid')->after('status');

            $table->timestamp('payment_approved_at')->nullable();
            $table->foreignId('payment_approved_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('payment_note')->nullable();
        });
    }
};

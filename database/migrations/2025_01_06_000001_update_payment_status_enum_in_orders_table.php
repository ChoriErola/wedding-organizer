<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL, kita perlu mengubah enum secara manual
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid', 'pending', 'approved', 'rejected', 'paid in progress', 'paid completed') DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid', 'pending', 'approved', 'rejected') DEFAULT 'unpaid'");
    }
};

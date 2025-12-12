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
        Schema::table('order_services', function (Blueprint $table) {
            // Relasi ke service (tetap nullable demi histori)
            // use singular `package_id` for the foreign key, not `packages_id`
            $table->foreignId('package_id')
                ->after('service_id')
                ->nullable()
                ->constrained('packages')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_services', function (Blueprint $table) {
            // reverse the above: drop foreign and column if exist
            if (Schema::hasColumn('order_services', 'package_id')) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            }
        });
    }
};

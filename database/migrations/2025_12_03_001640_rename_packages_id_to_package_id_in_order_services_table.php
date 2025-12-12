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
            if (Schema::hasColumn('order_services', 'packages_id')) {
                $table->dropForeign(['packages_id']);
                $table->renameColumn('packages_id', 'package_id');
                $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_services', function (Blueprint $table) {
            //
        });
    }
};

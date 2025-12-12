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
        Schema::create('order_services', function (Blueprint $table) {
            $table->id();
            // Relasi ke order
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // Relasi ke service (tetap nullable demi histori)
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            // Snapshot data service saat di-order
            $table->string('service_name');
            $table->decimal('price', 15, 2);

            // Bisa dikembangkan (qty, durasi, dll)
            // $table->unsignedInteger('quantity')->default(1);

            // Penanda
            $table->boolean('is_required')->default(false); // dari paket atau pilihan customer
            $table->boolean('is_custom')->default(false);   // ditambah customer sendiri
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};

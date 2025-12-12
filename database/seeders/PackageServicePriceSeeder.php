<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageServicePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all pivot records: set value_price from services.harga_layanan
        DB::statement('
            UPDATE packages_services ps
            JOIN services s ON ps.service_id = s.id
            SET ps.value_price = s.harga_layanan
            WHERE ps.value_price = 0
        ');

        $this->command->info('✓ Updated pivot table value_price from services.harga_layanan');
    }
}

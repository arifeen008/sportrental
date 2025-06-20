<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PackageRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('package_rates')->truncate();

        $now = Carbon::now();

        $rates = [
            // --- การกุศล ---
            [
                'rental_type' => 'การกุศล',
                'package_name' => 'เหมา 2 สนาม',
                'base_price' => 5000.00,
                'overtime_price_per_hour_per_field' => 400.00,
                'overtime_max_end_time' => '21:00:00',
            ],
            [
                'rental_type' => 'การกุศล',
                'package_name' => 'สนามกลางแจ้ง',
                'base_price' => 2000.00,
                'overtime_price_per_hour_per_field' => 400.00,
                'overtime_max_end_time' => '21:00:00',
            ],
            [
                'rental_type' => 'การกุศล',
                'package_name' => 'สนามหลังคา',
                'base_price' => 3500.00,
                'overtime_price_per_hour_per_field' => 400.00,
                'overtime_max_end_time' => '21:00:00',
            ],
            // --- รายการแข่งขัน ---
            [
                'rental_type' => 'รายการแข่งขัน',
                'package_name' => 'เหมา 2 สนาม',
                'base_price' => 9000.00,
                'overtime_price_per_hour_per_field' => 500.00,
                'overtime_max_end_time' => '22:00:00',
            ],
            [
                'rental_type' => 'รายการแข่งขัน',
                'package_name' => 'สนามกลางแจ้ง',
                'base_price' => 4000.00,
                'overtime_price_per_hour_per_field' => 500.00,
                'overtime_max_end_time' => '22:00:00',
            ],
            [
                'rental_type' => 'รายการแข่งขัน',
                'package_name' => 'สนามหลังคา',
                'base_price' => 6000.00,
                'overtime_price_per_hour_per_field' => 500.00,
                'overtime_max_end_time' => '22:00:00',
            ],
        ];

        foreach ($rates as &$rate) {
            $rate['created_at'] = $now;
            $rate['updated_at'] = $now;
        }

        DB::table('package_rates')->insert($rates);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('membership_tiers')->truncate();

        $now = Carbon::now();
        $common_condition = 'ไม่สามารถนำไปใช้แข่งขัน, ต้องจองล่วงหน้า 3 วัน';

        $tiers = [
            [
                'tier_name' => 'สมาชิก 10 ชม.',
                'price' => 2500.00,
                'included_hours' => 10,
                'validity_days' => 30,
                'special_perks' => null,
                'conditions' => $common_condition,
            ],
            [
                'tier_name' => 'สมาชิก 15 ชม.',
                'price' => 3500.00,
                'included_hours' => 15,
                'validity_days' => 45,
                'special_perks' => null,
                'conditions' => $common_condition,
            ],
            [
                'tier_name' => 'สมาชิก 20 ชม.',
                'price' => 4500.00,
                'included_hours' => 20,
                'validity_days' => 60,
                'special_perks' => null,
                'conditions' => $common_condition,
            ],
            [
                'tier_name' => 'VIP 20 ชม.',
                'price' => 5000.00,
                'included_hours' => 20,
                'validity_days' => 60,
                'special_perks' => 'พร้อมน้ำดื่ม 2 แพ็คฟรี',
                'conditions' => $common_condition,
            ],
        ];

        foreach ($tiers as &$tier) {
            $tier['created_at'] = $now;
            $tier['updated_at'] = $now;
        }

        DB::table('membership_tiers')->insert($tiers);
    }
}
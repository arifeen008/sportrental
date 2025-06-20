<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HourlyRateSeeder::class,     // รายชั่วโมง
            PackageRateSeeder::class,    // เหมาวัน
            MembershipTierSeeder::class, // บัตรสมาชิก
        ]);
    }
}
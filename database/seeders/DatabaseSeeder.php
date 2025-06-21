<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // เพิ่มการ import DB

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- ส่วนที่เพิ่มเข้ามาเพื่อแก้ปัญหา ---

        // ปิดการตรวจสอบ Foreign Key ก่อนรัน Seeder
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ------------------------------------

        $this->call([
            UserSeeder::class, // <-- เพิ่มเข้ามาเป็นอันแรก
            FieldTypeSeeder::class,
            HourlyRateSeeder::class,
            PackageRateSeeder::class,
            MembershipTierSeeder::class,
        ]);
        
        // --- ส่วนที่เพิ่มเข้ามาเพื่อแก้ปัญหา ---

        // เปิดการตรวจสอบ Foreign Key กลับมาเหมือนเดิม
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ------------------------------------
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('fields')->insert([
            [
                'name' => 'สนามกลางแจ้งหลัก', // ชื่อสนามกลางแจ้ง
                'field_type' => 'outdoor', // ประเภท: กลางแจ้ง
                'description' => 'สนามฟุตบอลกลางแจ้ง ขนาดมาตรฐานสำหรับจองรายชั่วโมง/เหมาวัน',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'สนามหลังคาหลัก', // ชื่อสนามหลังคา
                'field_type' => 'covered', // ประเภท: มีหลังคา
                'description' => 'สนามฟุตบอลมีหลังคา ขนาดมาตรฐานสำหรับจองรายชั่วโมง/เหมาวัน',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 'เหมา 2 สนาม' ไม่ใช่ประเภทสนาม แต่เป็นเงื่อนไขการจองที่ใช้ 2 สนามจริง (outdoor/covered)
            // ดังนั้น จะไม่ถูกเพิ่มเป็นสนามในตาราง 'fields' นี้
        ]);
    }
}


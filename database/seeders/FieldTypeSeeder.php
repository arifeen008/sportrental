<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FieldTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('field_types')->truncate();
        
        $now = Carbon::now();

        DB::table('field_types')->insert([
            [
                'name' => 'สนามกลางแจ้ง',
                'description' => 'สนามฟุตซอลหญ้าเทียมกลางแจ้ง ขนาดมาตรฐานสำหรับ 7 คน',
                'size' => '25m x 45m',
                'capacity' => '10 คน',
                'status' => 'ใช้งานได้',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'สนามหลังคา',
                'description' => 'สนามฟุตซอลหญ้าเทียมในร่ม มีหลังคากันแดดกันฝน สำหรับ 7 คน',
                'size' => '25m x 45m',
                'capacity' => '10 คน',
                'status' => 'ใช้งานได้',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
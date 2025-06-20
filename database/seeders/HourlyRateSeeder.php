<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HourlyRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ดึง ID ของประเภทสนามที่เราเพิ่งสร้าง
        $outdoorField = DB::table('field_types')->where('name', 'สนามกลางแจ้ง')->first();
        $indoorField = DB::table('field_types')->where('name', 'สนามหลังคา')->first();

        // ถ้าไม่พบ ให้หยุดทำงานเพื่อป้องกัน error
        if (!$outdoorField || !$indoorField) {
            $this->command->error('Field types not found. Please run FieldTypeSeeder first.');
            return;
        }

        DB::table('hourly_rates')->truncate();
        $now = Carbon::now();
        $allRates = [];

        // 2. กำหนดกลุ่มราคาและวันแบบอัตโนมัติ
        $priceGroups = [
            [
                'days' => ['อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์'],
                'slots' => [
                    ['start' => '09:00:00', 'end' => '18:00:00', 'outdoor_price' => 350, 'indoor_price' => 450],
                    ['start' => '18:00:00', 'end' => '22:00:00', 'outdoor_price' => 600, 'indoor_price' => 600],
                ]
            ],
            [
                'days' => ['เสาร์', 'อาทิตย์'],
                'slots' => [
                    ['start' => '09:00:00', 'end' => '18:00:00', 'outdoor_price' => 400, 'indoor_price' => 500],
                    ['start' => '18:00:00', 'end' => '22:00:00', 'outdoor_price' => 700, 'indoor_price' => 700],
                ]
            ]
        ];

        // 3. วนลูปสร้างข้อมูลทั้งหมด
        foreach ($priceGroups as $group) {
            foreach ($group['days'] as $day) {
                foreach ($group['slots'] as $slot) {
                    // ราคาสำหรับสนามกลางแจ้ง
                    $allRates[] = [
                        'field_type_id' => $outdoorField->id,
                        'day_of_week'   => $day, // <-- ใช้ชื่อคอลัมน์ที่ถูกต้อง 'day_of_week'
                        'start_time'    => $slot['start'],
                        'end_time'      => $slot['end'],
                        'price_per_hour'=> $slot['outdoor_price'],
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                    // ราคาสำหรับสนามหลังคา
                    $allRates[] = [
                        'field_type_id' => $indoorField->id,
                        'day_of_week'   => $day, // <-- ใช้ชื่อคอลัมน์ที่ถูกต้อง 'day_of_week'
                        'start_time'    => $slot['start'],
                        'end_time'      => $slot['end'],
                        'price_per_hour'=> $slot['indoor_price'],
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }
            }
        }
        
        // 4. เพิ่มข้อมูลทั้งหมดลง DB ในครั้งเดียว
        DB::table('hourly_rates')->insert($allRates);
    }
}
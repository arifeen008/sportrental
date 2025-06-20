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
        

        $now = Carbon::now();
        $allRates = [];

        // กำหนดกลุ่มวันและราคาตั้งต้น
        $priceGroups = [
            [
                'days' => ['อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์'],
                'times' => [
                    ['start' => '09:00:00', 'end' => '18:00:00', 'prices' => ['กลางแจ้ง' => 350, 'หลังคา' => 450]],
                    ['start' => '18:00:00', 'end' => '22:00:00', 'prices' => ['กลางแจ้ง' => 600, 'หลังคา' => 600]],
                ]
            ],
            [
                'days' => ['เสาร์', 'อาทิตย์'],
                'times' => [
                    ['start' => '09:00:00', 'end' => '18:00:00', 'prices' => ['กลางแจ้ง' => 400, 'หลังคา' => 500]],
                    ['start' => '18:00:00', 'end' => '22:00:00', 'prices' => ['กลางแจ้ง' => 700, 'หลังคา' => 700]],
                ]
            ]
        ];

        // วนลูปเพื่อสร้างข้อมูลทั้งหมด
        foreach ($priceGroups as $group) {
            foreach ($group['days'] as $day) {
                foreach ($group['times'] as $timeSlot) {
                    foreach ($timeSlot['prices'] as $fieldType => $price) {
                        $allRates[] = [
                            'field_type' => $fieldType,
                            'day_of_week' => $day,
                            'start_time' => $timeSlot['start'],
                            'end_time' => $timeSlot['end'],
                            'price_per_hour' => $price,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        // เพิ่มข้อมูลทั้งหมดลง DB ในครั้งเดียว
        DB::table('hourly_rates')->insert($allRates);
    }
}
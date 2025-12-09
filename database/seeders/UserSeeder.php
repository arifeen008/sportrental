<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Import Model User

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'Admin SKF',
            'email'             => 'admin@example.com',
            'id_card'           => '4805248673254', // เพิ่มเข้ามา (ใช้ข้อมูลสมมติ)
            'phone_number'      => '0812345678',    // เพิ่มเข้ามา (ใช้ข้อมูลสมมติ)
            'role'              => 'admin',
            'email_verified_at' => now(),
            'password'          => Hash::make('adminpassword'),
        ]);

        // 2. สร้าง User ทั่วไป
        User::create([
            'name'              => 'Test User',
            'email'             => 'user@example.com',
            'id_card'           => '6994318440215', // เพิ่มเข้ามา (ใช้ข้อมูลสมมติ)
            'phone_number'      => '0987654321',    // เพิ่มเข้ามา (ใช้ข้อมูลสมมติ)
            'role'              => 'user',
            'email_verified_at' => now(),
            'password'          => Hash::make('userpassword'),
        ]);
    }
}

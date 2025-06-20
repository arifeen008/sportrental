<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('tier_name'); // เช่น 'สมาชิก 10 ชม.', 'VIP 20 ชม.'
            $table->decimal('price', 10, 2); // ราคาของบัตรสมาชิก
            $table->integer('included_hours'); // จำนวนชั่วโมงที่ได้รับ
            $table->integer('validity_days'); // บัตรมีอายุกี่วันนับจากวันที่ซื้อ
            $table->string('applicable_days')->default('อังคาร - อาทิตย์'); // วันที่สามารถใช้งานได้
            $table->time('normal_hours_start')->default('10:00:00'); // เวลาปกติ เริ่มต้น
            $table->time('normal_hours_end')->default('18:00:00'); // เวลาปกติ สิ้นสุด
            $table->decimal('overtime_hour_multiplier', 3, 1)->default(2.0); // ตัวคูณการหักชั่วโมงช่วงนอกเวลา (1 ชม. = 2 ชม.)
            $table->text('special_perks')->nullable(); // สิทธิประโยชน์พิเศษ เช่น 'พร้อมน้ำดื่ม 2 แพ็คฟรี'
            $table->text('conditions')->nullable(); // เงื่อนไขเพิ่มเติม
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
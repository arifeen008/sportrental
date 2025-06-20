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
        Schema::create('package_rates', function (Blueprint $table) {
            $table->id();
            $table->string('rental_type'); // ประเภทการเช่า: 'การกุศล', 'รายการแข่งขัน'
            $table->string('package_name'); // แพ็กเกจ: 'เหมา 2 สนาม', 'สนามกลางแจ้ง', 'สนามหลังคา'
            $table->decimal('base_price', 10, 2); // ราคาเหมาตั้งต้น
            $table->time('base_start_time')->default('08:00:00'); // เวลาเริ่มของราคาเหมา
            $table->time('base_end_time')->default('18:00:00');   // เวลาสิ้นสุดของราคาเหมา
            $table->decimal('overtime_price_per_hour_per_field', 10, 2); // ราคาล่วงเวลาต่อชั่วโมง/สนาม
            $table->time('overtime_max_end_time'); // เวลาที่สามารถใช้สนามได้นานสุด (รวมล่วงเวลา)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_rates');
    }
};
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // เราจะเพิ่ม Foreign Keys ใน Migration แยกต่างหาก
            // เพื่อให้แน่ใจว่าตารางที่ถูกอ้างอิงถูกสร้างขึ้นก่อน

            $table->dateTime('start_time');                          // เวลาเริ่มต้นการจองจริง
            $table->dateTime('end_time');                            // เวลาสิ้นสุดการจองจริง
            $table->decimal('total_price', 10, 2);                   // ราคารวมที่คำนวณและบันทึก ณ เวลาที่จอง
            $table->string('status', 50)->default('pending');        // 'pending', 'confirmed', 'cancelled'
            $table->string('payment_status', 50)->default('unpaid'); // 'unpaid', 'paid', 'refunded'
            $table->string('payment_method')->nullable();            // 'cash', 'bank transfer', 'credit card'
            $table->text('notes')->nullable();                       // หมายเหตุ
            $table->integer('num_participants')->nullable();         // จำนวนผู้เข้าร่วม

                                                                                 // เก็บรายละเอียดของประเภทการจองที่ใช้ ณ ตอนนั้น
                                                                                 // เพื่อให้ประวัติการจองมีความสมบูรณ์ แม้ BookingType ถูกแก้ไขหรือลบไปแล้ว
            $table->string('booking_type_name_at_booking');                      // ชื่อประเภทการจอง (เช่น "รายชั่วโมง: กลางแจ้ง จ-ศ 09.00-18.00 น.")
            $table->decimal('booking_type_price_at_booking', 10, 2)->nullable(); // ราคาที่ใช้สำหรับ bookingType นั้นๆ ณ ตอนที่จอง
            $table->string('booking_type_category_at_booking', 50)->nullable();  // หมวดหมู่ประเภทการจองที่ใช้ ณ ตอนนั้น

            $table->timestamps();
            $table->softDeletes(); // สำหรับ Soft Delete ข้อมูลการจอง
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

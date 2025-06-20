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

            // --- ข้อมูลหลัก: ใคร จองอะไร ---
            $table->foreignId('user_id')->constrained('users')->comment('ผู้ที่ทำการจอง');
            $table->foreignId('field_type_id')->constrained('field_types')->comment('ประเภทสนามที่จอง');

            // --- ข้อมูลเวลา: จองวันไหน เวลาไหนถึงไหน ---
            $table->date('booking_date')->comment('วันที่ต้องการใช้สนาม');
            $table->time('start_time')->comment('เวลาเริ่มใช้สนาม');
            $table->time('end_time')->comment('เวลาสิ้นสุดการใช้สนาม');

            // --- ประเภทการจองและสถานะ ---
            $table->string('booking_type')->comment("ประเภทการจอง เช่น 'hourly', 'daily_package', 'membership'");
            $table->string('status')->default('pending')->comment("สถานะการจอง เช่น 'pending', 'confirmed', 'completed', 'cancelled'");

            // --- ข้อมูลด้านการเงิน: เก็บรายละเอียดราคาที่คำนวณได้ ---
            $table->decimal('base_price', 10, 2)->default(0)->comment('ราคาพื้นฐานตามแพ็กเกจ/รายชั่วโมง');
            $table->decimal('overtime_charges', 10, 2)->default(0)->comment('ค่าใช้จ่ายล่วงเวลา (ถ้ามี)');
            $table->decimal('other_charges', 10, 2)->default(0)->comment('ค่าใช้จ่ายอื่นๆ (ถ้ามี)');
            $table->decimal('discount', 10, 2)->default(0)->comment('ส่วนลด');
            $table->decimal('total_price', 10, 2)->comment('ราคาสุทธิที่ต้องชำระ');
            $table->string('payment_status')->default('unpaid')->comment("สถานะการชำระเงิน เช่น 'unpaid', 'paid', 'refunded'");

            // --- ส่วนเฉพาะสำหรับการจองโดยใช้บัตรสมาชิก ---
            // หมายเหตุ: user_membership_id จะอ้างอิงถึงตารางที่เก็บว่า user คนไหนถือบัตรสมาชิกใบไหน ซึ่งอาจจะต้องสร้างในอนาคต
            $table->unsignedBigInteger('user_membership_id')->nullable()->comment('ID ของบัตรสมาชิกที่ใช้จอง (ถ้ามี)');
            $table->decimal('hours_deducted', 4, 2)->nullable()->comment('จำนวนชั่วโมงที่ถูกหักจากบัตรสมาชิก');

            // --- ข้อมูลเพิ่มเติม ---
            $table->text('notes')->nullable()->comment('หมายเหตุเพิ่มเติมจากการจอง');
            $table->json('price_calculation_details')->nullable()->comment('เก็บรายละเอียดกฎราคาที่ใช้คำนวณ (สำหรับตรวจสอบ)');

            $table->timestamps(); // created_at คือวันที่ทำการจอง
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
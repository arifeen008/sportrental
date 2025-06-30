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
            // === ข้อมูลหลัก ===
            $table->id();
            $table->string('booking_code')->unique()->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('field_type_id')->nullable()->constrained('field_types');

            // === ข้อมูลการจอง ===
            $table->string('booking_type');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_in_hours')->nullable();

            // === สถานะ (รวมทุกอย่างไว้ในคอลัมน์เดียว) ===
            $table->string('status')->default('pending_payment'); // pending_payment, verifying, paid, rejected, cancelled, completed
            $table->timestamp('expires_at')->nullable()->comment('เวลาหมดอายุสำหรับสถานะ pending_payment');

            // === ข้อมูลการเงิน ===
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('overtime_charges', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0); // เผื่อไว้ในอนาคต
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);

            // === ข้อมูลการชำระเงิน และ บัตรสมาชิก ===
            $table->string('slip_image_path')->nullable();
            $table->unsignedBigInteger('user_membership_id')->nullable();
            $table->decimal('hours_deducted', 4, 2)->nullable();

            // === ข้อมูลการขอเลื่อนวัน ===
            $table->string('reschedule_status')->nullable(); // requested, approved, rejected
            $table->text('reschedule_reason')->nullable();
            $table->date('new_booking_date')->nullable();
            $table->time('new_start_time')->nullable();
            $table->time('new_end_time')->nullable();

            // === ข้อมูลเพิ่มเติม ===
            $table->text('notes')->nullable();
            $table->json('price_calculation_details')->nullable();
            $table->timestamps();
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

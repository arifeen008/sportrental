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
        Schema::table('bookings', function (Blueprint $table) {
                            // เพิ่มคอลัมน์สำหรับเก็บสถานะการขอเลื่อน
            $table->string('reschedule_status')->nullable()->after('status'); // e.g., 'requested', 'approved', 'rejected'
                                                                              // เหตุผลที่ผู้ใช้ขอเลื่อน
            $table->text('reschedule_reason')->nullable()->after('reschedule_status');
            // วันที่และเวลาใหม่ที่ผู้ใช้เสนอ
            $table->date('new_booking_date')->nullable()->after('reschedule_reason');
            $table->time('new_start_time')->nullable()->after('new_booking_date');
            $table->time('new_end_time')->nullable()->after('new_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};

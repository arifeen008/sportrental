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
            // เราจะเปลี่ยนชื่อ payment_status เดิมให้เป็น status ที่ครอบคลุมกว่า
            // และเพิ่มเวลาหมดอายุสำหรับการจองที่ยังไม่จ่ายเงิน
            $table->renameColumn('payment_status', 'status');
            $table->timestamp('expires_at')->nullable()->after('status');
        });
    }

// ในฟังก์ชัน down() เราจะทำย้อนกลับ
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('status', 'payment_status');
            $table->dropColumn('expires_at');
        });
    }
};

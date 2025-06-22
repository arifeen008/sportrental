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
            // เพิ่มคอลัมน์ string ชื่อ slip_image_path
            // สามารถเป็นค่าว่างได้ (nullable)
            // และให้ตำแหน่งอยู่หลังคอลัมน์ payment_status
            $table->string('slip_image_path')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('slip_image_path');
        });
    }
};

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
        Schema::create('hourly_rates', function (Blueprint $table) {
            $table->id();
            $table->string('field_type'); // เช่น 'กลางแจ้ง', 'หลังคา'
            $table->string('day_of_week'); // เปลี่ยนจาก day_group เป็น day_of_week (เช่น 'อังคาร', 'พุธ', 'พฤหัสบดี')
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price_per_hour', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hourly_rates');
    }
};
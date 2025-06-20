<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hourly_rates', function (Blueprint $table) {
            // 1. เพิ่มคอลัมน์ใหม่สำหรับ Foreign Key
            $table->unsignedBigInteger('field_type_id')->after('id');

            // 2. กำหนด Foreign Key constraint
            $table->foreign('field_type_id')->references('id')->on('field_types');

            // 3. ลบคอลัมน์เก่าที่เป็น string ทิ้ง
            $table->dropColumn('field_type');
        });
    }

    public function down(): void
    {
        Schema::table('hourly_rates', function (Blueprint $table) {
            // สำหรับการ rollback: ทำย้อนกลับ
            $table->string('field_type')->after('id');
            $table->dropForeign(['field_type_id']);
            $table->dropColumn('field_type_id');
        });
    }
};
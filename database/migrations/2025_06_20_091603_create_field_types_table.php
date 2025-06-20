<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อประเภทสนาม เช่น 'สนามกลางแจ้ง', 'สนามหลังคา'
            $table->text('description')->nullable(); // คำอธิบายเพิ่มเติม
            $table->string('size')->nullable(); // ขนาดสนาม เช่น '25m x 45m'
            $table->string('capacity')->nullable(); // รองรับผู้เล่น เช่น '7 คน'
            $table->string('status')->default('ใช้งานได้'); // สถานะ เช่น 'ใช้งานได้', 'ปิดปรับปรุง'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_types');
    }
};
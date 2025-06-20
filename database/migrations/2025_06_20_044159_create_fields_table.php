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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อสนาม เช่น 'สนาม A', 'สนาม B'
            $table->string('field_type', 50); // ประเภทสนาม: 'outdoor' (กลางแจ้ง), 'covered' (หลังคา)
            $table->text('description')->nullable(); // รายละเอียดสนามเพิ่มเติม
            $table->boolean('is_active')->default(true); // สถานะเปิดใช้งานสนาม
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};


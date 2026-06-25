<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained()->cascadeOnDelete();
            // Bagian Plan (pagi)
            $table->text('description');
            $table->enum('priority', ['tinggi', 'sedang', 'rendah'])->default('sedang');
            // Bagian Report (sore)
            $table->enum('status', ['selesai', 'sebagian', 'tidak'])->nullable();
            $table->text('realisasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

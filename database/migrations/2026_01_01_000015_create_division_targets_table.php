<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('division_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->foreignId('set_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('plan_target_pct')->default(80);
            $table->unsignedTinyInteger('report_target_pct')->default(80);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['division_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_targets');
    }
};

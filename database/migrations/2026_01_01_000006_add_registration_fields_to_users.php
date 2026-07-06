<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('division_id');
            $table->string('no_hp')->nullable()->after('jabatan');
            $table->enum('status', ['pending', 'manager_approved', 'active', 'rejected'])->default('active')->after('no_hp');
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jabatan', 'no_hp', 'status', 'rejection_reason']);
        });
    }
};

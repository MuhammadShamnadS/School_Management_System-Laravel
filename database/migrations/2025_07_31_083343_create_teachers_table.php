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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 🔹 One-to-One with users
            $table->string('phone', 15);
            $table->string('subject_specialization', 100);
            $table->string('employee_id', 20)->unique();
            $table->date('date_of_joining');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 🔹 One-to-One with users
        $table->string('phone', 15);
        $table->string('roll_number', 20)->unique();
        $table->string('student_class', 50);
        $table->date('date_of_birth');
        $table->date('admission_date');
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->foreignId('assigned_teacher_id')->nullable()->constrained('teachers')->onDelete('set null'); // 🔹 FK to teachers
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('students');
}
};

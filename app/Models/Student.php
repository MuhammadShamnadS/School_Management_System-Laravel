<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'roll_number',
        'student_class',
        'date_of_birth',
        'admission_date',
        'status',
        'assigned_teacher_id',
    ];

    // Each Student belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Each Student may belong to one Teacher
    public function assignedTeacher()
    {
        return $this->belongsTo(Teacher::class, 'assigned_teacher_id');
    }
}

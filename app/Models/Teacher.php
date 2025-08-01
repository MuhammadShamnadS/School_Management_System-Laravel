<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'subject_specialization',
        'employee_id',
        'date_of_joining',
        'status',
    ];

    // Each Teacher belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A Teacher can have many Students
    public function students()
    {
        return $this->hasMany(Student::class, 'assigned_teacher_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'password', 'role'
    ];

    protected $hidden = ['password'];

    //Relation with Teacher
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    //Relation with Student
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    //JWT Required Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}

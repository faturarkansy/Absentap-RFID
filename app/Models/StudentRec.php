<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRec extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    // public function student()
    // {
    //     return $this->hasOne(Student::class, 'nik', 'student_nik');
    // }
}
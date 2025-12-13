<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruiter_id',
        'title',
        'description',
        'difficulty',
        'working_hours',
        'payment'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'difficulty',
        'start_at',
        'correct_answer',
    ];

    protected $casts = [
        'start_at' => 'array',
        'correct_answer' => 'array',
    ];
}

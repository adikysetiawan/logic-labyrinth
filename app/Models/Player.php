<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'university',
        'program',
    ];

    public function gamePlays()
    {
        return $this->hasMany(\App\Models\GamePlay::class, 'player_id');
    }
}

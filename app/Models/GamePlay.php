<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'level_id',
        'difficulty',
        'success',
        'time_seconds',
        'base_points',
        'score',
    ];

    protected $casts = [
        'success' => 'boolean',
        'time_seconds' => 'integer',
        'base_points' => 'integer',
        'score' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function (GamePlay $gamePlay) {
            // Base points depend on difficulty when success
            // easy => 100, hard => 120; if failed => 0
            if ($gamePlay->success) {
                $diff = strtolower((string) $gamePlay->difficulty);
                $gamePlay->base_points = ($diff === 'hard') ? 120 : 100;
            } else {
                $gamePlay->base_points = 0;
            }

            // Ensure time_seconds is set
            $time = max(0, (int) ($gamePlay->time_seconds ?? 0));
            $gamePlay->time_seconds = $time;

            // Score formula: score = base_points - (time_seconds / 60)
            $score = $gamePlay->base_points - ($time / 60);
            // Keep two decimals, not below zero
            $gamePlay->score = max(0, round($score, 2));
        });
    }

    public function recalcScore(): void
    {
        if ($this->success) {
            $diff = strtolower((string) $this->difficulty);
            $this->base_points = ($diff === 'hard') ? 120 : 100;
        } else {
            $this->base_points = 0;
        }
        $time = max(0, (int) ($this->time_seconds ?? 0));
        $this->score = max(0, round($this->base_points - ($time / 60), 2));
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}

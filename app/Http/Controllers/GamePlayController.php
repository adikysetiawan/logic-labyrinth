<?php

namespace App\Http\Controllers;

use App\Models\GamePlay;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GamePlayController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'level_id' => ['required','integer'],
            'success' => ['required','boolean'],
            'time_seconds' => ['nullable','integer','min:0'],
        ]);

        $playerId = session('player_id');
        $difficulty = session('difficulty');

        if (!$playerId || !$difficulty) {
            return response()->json([
                'message' => 'Player session not found.'
            ], 422);
        }

        $play = GamePlay::create([
            'player_id' => $playerId,
            'level_id' => $data['level_id'],
            'difficulty' => $difficulty,
            'success' => $data['success'],
            'time_seconds' => $data['time_seconds'] ?? 0,
        ]);

        // Enforce single-play flow: clear temp session so next time user must register again
        session()->forget(['player_id', 'difficulty']);

        return response()->json([
            'message' => 'Recorded',
            'id' => $play->id,
            'base_points' => $play->base_points,
            'score' => (float) $play->score,
            'next' => route('player.register.show'),
        ]);
    }
}

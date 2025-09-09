<?php

namespace App\Http\Controllers;

use App\Models\GamePlay;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    // Top skor mingguan seluruh pemain (gabungan kesulitan)
    // Param opsional: ?limit=10
    public function weeklyTop(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        if ($limit < 1 || $limit > 100) { $limit = 10; }

        $start = now()->startOfWeek();
        $end   = now()->endOfWeek();

        // Subquery: skor terbaik per pemain pada minggu berjalan
        $bestPerPlayer = GamePlay::query()
            ->selectRaw('player_id, MAX(score) AS best_score')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('player_id');

        // Join ke gameplay untuk ambil catatan dengan skor terbaik,
        // jika ada lebih dari satu catatan dengan skor yang sama,
        // pilih yang time_seconds paling kecil, jika masih seri, created_at paling awal.
        $rows = GamePlay::query()
            ->joinSub($bestPerPlayer, 'b', function ($join) {
                $join->on('game_plays.player_id', '=', 'b.player_id')
                     ->on('game_plays.score', '=', 'b.best_score');
            })
            ->whereBetween('game_plays.created_at', [$start, $end])
            ->selectRaw('game_plays.player_id, b.best_score, game_plays.time_seconds, game_plays.difficulty, game_plays.created_at')
            ->orderByDesc('b.best_score')
            ->orderBy('game_plays.time_seconds')
            ->orderBy('game_plays.created_at')
            ->with('player:id,name,university,program')
            ->get()
            ->unique('player_id')
            ->take($limit)
            ->values()
            ->map(function ($row) {
                return [
                    'player_id'    => $row->player_id,
                    'name'         => optional($row->player)->name,
                    'university'   => optional($row->player)->university,
                    'program'      => optional($row->player)->program,
                    'best_score'   => (float) $row->best_score,
                    'time_seconds' => (int) $row->time_seconds,
                    'difficulty'   => $row->difficulty,
                    'at'           => $row->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'range' => [
                'start' => $start->toDateTimeString(),
                'end'   => $end->toDateTimeString(),
            ],
            'count' => $rows->count(),
            'data'  => $rows,
        ]);
    }
}

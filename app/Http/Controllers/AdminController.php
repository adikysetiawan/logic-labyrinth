<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Level;
use App\Models\GamePlay;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function players(Request $request)
    {
        $q = $request->get('q');
        $sort = $request->get('sort');
        $playersQuery = \App\Models\Player::query()
            ->when($q, function($query, $q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('university', 'like', "%$q%")
                        ->orWhere('program', 'like', "%$q%") ;
                });
            })
            ->withCount([
                'gamePlays as total_main' => fn($q) => $q,
                'gamePlays as total_berhasil' => fn($q) => $q->where('success', true),
                'gamePlays as total_gagal' => fn($q) => $q->where('success', false),
            ])
            // Aggregate total points from each gameplay's score column
            ->withSum('gamePlays as total_score', 'score')
            ->with(['gamePlays' => function($q){ $q->latest()->limit(1); }]);

        // Sorting
        switch ($sort) {
            case 'name':
                $playersQuery->orderBy('name');
                break;
            case '-name':
                $playersQuery->orderByDesc('name');
                break;
            case 'university':
                $playersQuery->orderBy('university');
                break;
            case '-university':
                $playersQuery->orderByDesc('university');
                break;
            case 'program':
                $playersQuery->orderBy('program');
                break;
            case '-program':
                $playersQuery->orderByDesc('program');
                break;
            case 'total_main':
                $playersQuery->orderBy('total_main');
                break;
            case '-total_main':
                $playersQuery->orderByDesc('total_main');
                break;
            case 'total_berhasil':
                $playersQuery->orderBy('total_berhasil');
                break;
            case '-total_berhasil':
                $playersQuery->orderByDesc('total_berhasil');
                break;
            case 'total_gagal':
                $playersQuery->orderBy('total_gagal');
                break;
            case '-total_gagal':
                $playersQuery->orderByDesc('total_gagal');
                break;
            case 'score':
                // total points ascending
                $playersQuery->orderBy('total_score');
                break;
            case '-score':
                // total points descending
                $playersQuery->orderByDesc('total_score');
                break;
            default:
                $playersQuery->orderByDesc('id');
        }

        $players = $playersQuery->paginate(20)->withQueryString();

        return view('admin.players', compact('players', 'q'));
    }

    public function levels()
    {
        $levels = Level::latest()->paginate(20);
        return view('admin.levels', compact('levels'));
    }

    // FORM actions for Levels (admin-only)
    public function storeLevel(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'difficulty' => ['required', 'in:easy,hard'],
            'start_x' => ['required', 'integer', 'between:0,4'],
            'start_y' => ['required', 'integer', 'between:0,4'],
            'x' => ['required', 'integer', 'between:0,4'],
            'y' => ['required', 'integer', 'between:0,4'],
        ]);

        Level::create([
            'code' => $data['code'],
            'difficulty' => $data['difficulty'],
            'start_at' => ['x' => (int) $data['start_x'], 'y' => (int) $data['start_y']],
            'correct_answer' => ['x' => (int) $data['x'], 'y' => (int) $data['y']],
        ]);

        return redirect()->route('admin.levels')->with('status', 'Level berhasil dibuat');
    }

    public function updateLevel(Request $request, Level $level)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'difficulty' => ['required', 'in:easy,hard'],
            'start_x' => ['required', 'integer', 'between:0,4'],
            'start_y' => ['required', 'integer', 'between:0,4'],
            'x' => ['required', 'integer', 'between:0,4'],
            'y' => ['required', 'integer', 'between:0,4'],
        ]);

        $level->update([
            'code' => $data['code'],
            'difficulty' => $data['difficulty'],
            'start_at' => ['x' => (int) $data['start_x'], 'y' => (int) $data['start_y']],
            'correct_answer' => ['x' => (int) $data['x'], 'y' => (int) $data['y']],
        ]);

        return redirect()->route('admin.levels')->with('status', 'Level berhasil diubah');
    }

    public function destroyLevel(Level $level)
    {
        $level->delete();
        return redirect()->route('admin.levels')->with('status', 'Level dihapus');
    }

    public function destroyPlayer(Player $player)
    {
        $player->delete();
        return redirect()->route('admin.players')->with('status', 'Pemain dihapus');
    }

    public function gamePlays(Request $request)
    {
        $query = GamePlay::query()->with(['player', 'level'])->latest();

        if ($d = $request->get('difficulty')) {
            $query->where('difficulty', $d);
        }
        if (($s = $request->get('success')) !== null && $s !== '') {
            $query->where('success', (bool) $s);
        }

        $plays = $query->paginate(20)->withQueryString();

        // Simple metrics
        $metrics = [
            'total' => GamePlay::count(),
            'success' => GamePlay::where('success', true)->count(),
            'failed' => GamePlay::where('success', false)->count(),
            'easy' => GamePlay::where('difficulty', 'easy')->count(),
            'hard' => GamePlay::where('difficulty', 'hard')->count(),
        ];

        return view('admin.game-plays', compact('plays', 'metrics'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Level::query()->latest();
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->string('difficulty'));
        }
        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'difficulty' => ['required', 'in:easy,hard'],
            'correct_answer' => ['required', 'array'], // expects { x: int, y: int }
        ]);

        $level = Level::create($data);

        return response()->json($level, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        return response()->json($level);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        $data = $request->validate([
            'code' => ['sometimes', 'required', 'string'],
            'difficulty' => ['sometimes', 'required', 'in:easy,hard'],
            'correct_answer' => ['sometimes', 'required', 'array'],
        ]);

        $level->update($data);

        return response()->json($level);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        $level->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Check player's submitted final position against the level's correct answer.
     */
    public function check(Request $request, Level $level)
    {
        $data = $request->validate([
            'x' => ['required', 'integer', 'between:0,4'],
            'y' => ['required', 'integer', 'between:0,4'],
        ]);

        $correct = $level->correct_answer ?? [];
        $isCorrect = isset($correct['x'], $correct['y'])
            && (int)$correct['x'] === (int)$data['x']
            && (int)$correct['y'] === (int)$data['y'];

        return response()->json([
            'success' => $isCorrect,
            'message' => $isCorrect ? 'Selamat! Anda berhasil!' : 'Ups, coba lagi!',
            'expected' => $correct,
            'received' => ['x' => (int)$data['x'], 'y' => (int)$data['y']],
        ]);
    }

    /**
     * Return a random level filtered by difficulty (from session or query).
     */
    public function random(Request $request)
    {
        $difficulty = $request->string('difficulty')->toString() ?: session('difficulty', 'easy');
        $level = Level::where('difficulty', $difficulty)->inRandomOrder()->first();
        if (!$level) {
            return response()->json(['message' => 'Level tidak tersedia untuk tingkat kesulitan ini.'], 404);
        }
        return response()->json($level);
    }
}

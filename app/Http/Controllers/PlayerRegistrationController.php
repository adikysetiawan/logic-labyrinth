<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerRegistrationController extends Controller
{
    public function show()
    {
        return view('player.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'university' => ['required', 'string', 'max:150'],
            'program' => ['required', 'string', 'max:100'],
        ]);

        $player = Player::create($data);

        // Save to session for non-auth flow
        session(['player_id' => $player->id]);

        // Redirect to difficulty selection page
        return redirect()->route('player.difficulty.show')->with('status', 'Pendaftaran berhasil. Pilih tingkat kesulitan.');
    }

    public function difficultyShow()
    {
        if (!session()->has('player_id')) {
            return redirect()->route('player.register.show');
        }
        return view('player.difficulty');
    }

    public function difficultyStore(Request $request)
    {
        if (!session()->has('player_id')) {
            return redirect()->route('player.register.show');
        }
        $data = $request->validate([
            'difficulty' => ['required', 'in:easy,hard'],
        ]);
        session(['difficulty' => $data['difficulty']]);
        return redirect()->route('play');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Score;
use Illuminate\Http\Request;

class TvController extends Controller
{
    public function lobby($roomId)
    {
        $room = Room::with(['quiz', 'participants.user'])->findOrFail($roomId);
        return view('tv.lobby', compact('room'));
    }

    public function game($roomId)
    {
        $room = Room::with(['quiz.questions.options'])->findOrFail($roomId);
        return view('tv.game', compact('room'));
    }

    public function results($roomId)
    {
        $room = Room::with(['quiz', 'scores.user'])->findOrFail($roomId);
        
        $scores = Score::where('room_id', $roomId)
            ->with('user')
            ->orderBy('total_score', 'desc')
            ->get();

        return view('tv.results', compact('room', 'scores'));
    }
}

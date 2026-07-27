<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomParticipant;
use App\Models\Score;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['quiz', 'participants.user', 'scores.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $historyData = $rooms->map(function ($room) {
            Score::syncForRoom($room->id);
            
            $scores = Score::where('room_id', $room->id)
                ->with('user')
                ->orderBy('total_score', 'desc')
                ->get();

            $winner = $scores->first();
            $second = $scores->get(1);
            $third = $scores->get(2);
            $last = ($scores->count() > 1) ? $scores->last() : null;

            return [
                'room' => $room,
                'participant_count' => $room->participants->count(),
                'winner' => $winner,
                'second' => $second,
                'third' => $third,
                'last' => $last,
            ];
        });

        $totalGames = $rooms->where('status', 'finished')->count();
        $totalPlayersJoined = RoomParticipant::count();
        $allTimeTopScore = Score::max('total_score') ?? 0;

        return view('admin.history.index', compact('historyData', 'totalGames', 'totalPlayersJoined', 'allTimeTopScore'));
    }
}

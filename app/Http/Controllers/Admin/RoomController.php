<?php

namespace App\Http\Controllers\Admin;

use App\Events\GameStarted;
use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Room;
use App\Models\Score;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function launch($quizId)
    {
        $quiz = Quiz::withCount('questions')->findOrFail($quizId);

        if (!in_array($quiz->status, ['draft', 'finished'])) {
            return back()->with('error', 'Quiz is currently waiting or ongoing.');
        }

        if ($quiz->questions_count === 0) {
            return back()->with('error', 'Cannot start a quiz with no questions');
        }

        // Check for existing waiting/ongoing room
        $existingRoom = Room::where('quiz_id', $quizId)
            ->whereIn('status', ['waiting', 'ongoing'])
            ->exists();

        if ($existingRoom) {
            return back()->with('error', 'An active room already exists for this quiz.');
        }

        $room = Room::create([
            'quiz_id' => $quiz->id,
            'room_code' => $quiz->room_code,
            'status' => 'waiting',
            'current_question' => 0,
        ]);

        $quiz->update(['status' => 'waiting']);

        return redirect()->route('admin.rooms.lobby', $room->id)->with('success', 'Arena launched! Waiting for players...');
    }

    public function lobby($roomId)
    {
        $room = Room::with(['quiz', 'participants.user'])->findOrFail($roomId);
        
        if ($room->status === 'ongoing') {
            return redirect()->route('admin.game.show', $room->id);
        }
        
        return view('admin.rooms.lobby', compact('room'));
    }

    public function startGame($roomId)
    {
        $room = Room::with(['quiz', 'participants'])->findOrFail($roomId);

        if ($room->status !== 'waiting') {
            return back()->with('error', 'Game has already started.');
        }

        $participantCount = $room->participants->count();

        if ($participantCount < 2) {
            return back()->with('error', 'Need at least 2 players to start');
        }

        if ($participantCount > $room->quiz->max_participants) {
            return back()->with('error', 'Participant limit exceeded.');
        }

        $room->update([
            'status' => 'ongoing',
            'started_at' => now(),
        ]);

        $room->quiz->update(['status' => 'ongoing']);

        // Create score records for all participants
        foreach ($room->participants as $participant) {
            Score::firstOrCreate([
                'room_id' => $room->id,
                'user_id' => $participant->user_id,
            ], [
                'total_score' => 0,
                'rank' => 0,
            ]);
        }

        broadcast(new GameStarted($room->id));

        return redirect()->route('admin.game.show', $room->id)->with('success', 'The Blitz has begun!');
    }

    public function getParticipants($roomId)
    {
        $room = Room::with('participants.user')->findOrFail($roomId);
        
        $participants = $room->participants->map(function ($participant) {
            return [
                'id' => $participant->user_id,
                'nickname' => $participant->user->nickname,
                'is_ready' => (bool)$participant->is_ready,
                'joined_at' => $participant->joined_at,
            ];
        });

        return response()->json($participants);
    }
}

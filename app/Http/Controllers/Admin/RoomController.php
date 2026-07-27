<?php

namespace App\Http\Controllers\Admin;

use App\Events\GameStarted;
use App\Events\QuestionStarted;
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

        if ($quiz->questions_count == 0) {
            return back()->with('error', 'Cannot launch an empty arena. Please add questions first.');
        }

        // Generate unique 6-character uppercase code
        do {
            $code = strtoupper(\Illuminate\Support\Str::random(6));
        } while (Room::where('room_code', $code)->where('status', '!=', 'finished')->exists());

        $room = Room::create([
            'quiz_id' => $quiz->id,
            'room_code' => $code,
            'status' => 'waiting',
        ]);

        $quiz->update([
            'status' => 'waiting',
            'room_code' => $code,
        ]);

        return redirect()->route('admin.rooms.lobby', $room->id)->with('success', 'Arena launched successfully!');
    }

    public function lobby($roomId)
    {
        $room = Room::with(['quiz', 'participants.user'])->findOrFail($roomId);
        
        if ($room->status !== 'waiting') {
            return redirect()->route('admin.game.show', $room->id);
        }
        
        return view('admin.rooms.lobby', compact('room'));
    }

    public function startGame($roomId)
    {
        $room = Room::with(['quiz.questions.options', 'participants'])->findOrFail($roomId);

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

        $firstQuestion = $room->quiz->questions->where('order_number', 1)->first();
        $totalQuestions = $room->quiz->questions->count();

        $room->update([
            'status' => 'ongoing',
            'started_at' => now(),
            'current_question' => 1,
            'question_started_at' => now(),
        ]);

        $room->quiz->update(['status' => 'ongoing']);

        Score::syncForRoom($room->id);

        broadcast(new GameStarted($room->id));

        if ($firstQuestion) {
            \App\Models\RoomParticipant::where('room_id', $room->id)->update(['is_ready' => false]);

            broadcast(new QuestionStarted(
                $room->id,
                $firstQuestion,
                1,
                $totalQuestions,
                $firstQuestion->time_limit
            ));
        }

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

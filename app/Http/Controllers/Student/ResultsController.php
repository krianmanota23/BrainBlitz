<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomParticipant;
use App\Models\Score;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultsController extends Controller
{
    public function show($roomId)
    {
        $room = Room::with(['quiz.questions', 'participants.user'])->findOrFail($roomId);
        
        $isParticipant = RoomParticipant::where('room_id', $roomId)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant) {
            return redirect()->route('student.join')->with('error', 'You were not a participant in this game.');
        }

        $myScore = Score::where('room_id', $roomId)->where('user_id', Auth::id())->first();
        $myAnswers = Answer::where('room_id', $roomId)->where('user_id', Auth::id())->get();
        
        $correctCount = $myAnswers->where('is_correct', true)->count();
        $totalQuestions = $room->quiz->questions->count();
        $accuracy = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $scoreboard = Score::where('room_id', $roomId)
            ->with('user')
            ->orderBy('total_score', 'desc')
            ->get();

        return view('student.results', compact('room', 'myScore', 'myAnswers', 'accuracy', 'scoreboard'));
    }
}

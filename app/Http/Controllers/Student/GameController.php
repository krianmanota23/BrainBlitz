<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Option;
use App\Models\Quiz;
use App\Models\Room;
use App\Models\RoomParticipant;
use App\Events\AnswerReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function dashboard()
    {
        $recentGames = RoomParticipant::where('user_id', Auth::id())
            ->whereHas('room', function($q) {
                $q->where('status', 'finished');
            })
            ->with(['room.quiz', 'room.scores' => function($q) {
                $q->where('user_id', Auth::id());
            }])
            ->orderBy('joined_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'games_played' => RoomParticipant::where('user_id', Auth::id())
                ->whereHas('room', fn($q) => $q->where('status', 'finished'))->count(),
            'best_rank' => \App\Models\Score::where('user_id', Auth::id())->min('rank') ?? 'N/A',
            'total_score' => \App\Models\Score::where('user_id', Auth::id())->sum('total_score'),
            'accuracy' => $this->calculateOverallAccuracy(),
        ];

        return view('student.dashboard', compact('recentGames', 'stats'));
    }

    private function calculateOverallAccuracy()
    {
        $totalAnswers = \App\Models\Answer::where('user_id', Auth::id())->count();
        if ($totalAnswers == 0) return 0;
        $correctAnswers = \App\Models\Answer::where('user_id', Auth::id())->where('is_correct', true)->count();
        return round(($correctAnswers / $totalAnswers) * 100);
    }

    public function showJoinForm()
    {
        return view('student.join', [
            'nickname' => Auth::user()->nickname
        ]);
    }

    public function joinRoom(Request $request)
    {
        // Sanitize pasted room code (strip spaces, newlines, special chars, and uppercase)
        $rawCode = $request->input('room_code', '');
        $cleanedCode = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $rawCode)));
        $request->merge(['room_code' => $cleanedCode]);

        $validated = $request->validate([
            'room_code' => ['required', 'string', 'size:6'],
        ]);

        $roomCode = $validated['room_code'];

        $room = Room::with('quiz', 'participants')
            ->where('room_code', $roomCode)
            ->whereIn('status', ['waiting', 'ongoing'])
            ->first();

        if (!$room) {
            $quiz = Quiz::where('room_code', $roomCode)->first();
            if ($quiz && $quiz->status === 'draft') {
                return back()->with('error', 'Arena has not been launched by the host yet. Please ask your host to click "Launch Game".');
            }

            return back()->with('error', 'Invalid room code or the game has already concluded.');
        }

        // Check if already joined
        $existing = RoomParticipant::where('room_id', $room->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            if ($room->status === 'ongoing') {
                return redirect()->route('student.game', $room->id);
            }
            return redirect()->route('student.rooms.waiting', $room->id);
        }

        if ($room->status !== 'waiting') {
            return back()->with('error', 'The battle has already started for this arena.');
        }

        // Check capacity
        if ($room->participants->count() >= $room->quiz->max_participants) {
            return back()->with('error', 'This arena has reached maximum capacity.');
        }

        RoomParticipant::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
        ]);

        return redirect()->route('student.rooms.waiting', $room->id);
    }

    public function waitingRoom($roomId)
    {
        $room = Room::with(['quiz', 'participants.user'])->findOrFail($roomId);
        
        $participant = RoomParticipant::where('room_id', $roomId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$participant) {
            return redirect()->route('student.join')->with('error', 'Permission denied.');
        }

        if ($room->status === 'ongoing') {
            return redirect()->route('student.game', $room->id);
        }

        $isParticipantReady = (bool) ($participant->is_ready ?? false);

        return view('student.waiting', compact('room', 'isParticipantReady'));
    }

    public function checkRoomStatus($roomId)
    {
        $room = Room::findOrFail($roomId);
        
        return response()->json([
            'status' => $room->status,
            'current_question' => $room->current_question,
            'question_started_at' => $room->question_started_at,
            'participant_count' => $room->participants()->count(),
            'max_participants' => $room->quiz->max_participants,
        ]);
    }

    public function game($roomId)
    {
        $room = Room::with(['quiz.questions.options'])->findOrFail($roomId);
        
        $isParticipant = RoomParticipant::where('room_id', $roomId)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant) {
            return redirect()->route('student.join')->with('error', 'You must join the room first.');
        }

        if ($room->status !== 'ongoing') {
            return redirect()->route('student.rooms.waiting', $room->id);
        }

        $currentQuestion = null;
        if ($room->current_question > 0) {
            $currentQuestion = $room->quiz->questions->where('order_number', $room->current_question)->first();
        }

        $alreadyAnswered = false;
        $selectedColor = null;
        if ($currentQuestion) {
            $userAnswer = Answer::where('room_id', $room->id)
                ->where('user_id', Auth::id())
                ->where('question_id', $currentQuestion->id)
                ->first();
            if ($userAnswer) {
                $alreadyAnswered = true;
                $selectedColor = $userAnswer->option?->color;
            }
        }

        $remainingTime = 0;
        if ($currentQuestion && $room->question_started_at) {
            $elapsed = \Illuminate\Support\Carbon::parse($room->question_started_at)->diffInSeconds(now());
            $remainingTime = max(0, $currentQuestion->time_limit - $elapsed);
        } elseif ($currentQuestion) {
            $remainingTime = $currentQuestion->time_limit;
        }

        return view('student.game', compact('room', 'currentQuestion', 'alreadyAnswered', 'selectedColor', 'remainingTime'));
    }

    public function submitAnswer(Request $request, $roomId)
    {
        $validated = $request->validate([
            'option_id' => 'required|exists:options,id',
        ]);

        $room = Room::findOrFail($roomId);
        
        if ($room->status !== 'ongoing') {
            return response()->json(['error' => 'Game is not in progress'], 403);
        }

        $currentQuestionNum = $room->current_question;
        $currentQuestion = $room->quiz->questions->where('order_number', $currentQuestionNum)->first();
        
        if (!$currentQuestion) {
            return response()->json(['error' => 'No active question'], 404);
        }

        // Check if already answered
        $alreadyAnswered = Answer::where('room_id', $room->id)
            ->where('user_id', Auth::id())
            ->where('question_id', $currentQuestion->id)
            ->exists();

        if ($alreadyAnswered) {
            return response()->json(['error' => 'Answer already submitted'], 403);
        }

        $option = Option::findOrFail($validated['option_id']);
        if ($option->question_id !== $currentQuestion->id) {
            return response()->json(['error' => 'Invalid option for this question'], 403);
        }

        $answer = Answer::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'question_id' => $currentQuestion->id,
            'option_id' => $option->id,
            'is_correct' => $option->is_correct,
            'points_earned' => 0, 
            'answered_at' => now(),
        ]);

        // Broadcast AnswerReceived with updated counts
        $counts = $this->calculateVoteCounts($room->id, $currentQuestion->id);
        broadcast(new AnswerReceived($room->id, $counts))->toOthers();

        return response()->json([
            'success' => true,
            'selected_color' => $option->color,
            'message' => 'Answer locked in!'
        ]);
    }

    private function calculateVoteCounts($roomId, $questionId)
    {
        $counts = Answer::where('room_id', $roomId)
            ->where('answers.question_id', $questionId)
            ->join('options', 'answers.option_id', '=', 'options.id')
            ->selectRaw('options.color, count(*) as count')
            ->groupBy('options.color')
            ->pluck('count', 'color')
            ->toArray();

        return [
            'red' => $counts['red'] ?? 0,
            'blue' => $counts['blue'] ?? 0,
            'yellow' => $counts['yellow'] ?? 0,
            'green' => $counts['green'] ?? 0,
        ];
    }
    public function markReady($roomId)
    {
        try {
            $participant = RoomParticipant::where('room_id', $roomId)
                ->where('user_id', \Auth::id())
                ->firstOrFail();
            
            $participant->update(['is_ready' => true]);
            
            $totalCount = RoomParticipant::where('room_id', $roomId)
                ->count();
                
            $readyCount = RoomParticipant::where('room_id', $roomId)
                ->where('is_ready', true)
                ->count();
            
            $allReady = $readyCount >= $totalCount 
                        && $totalCount > 0;
            
            broadcast(new \App\Events\PlayerReady(
                roomId: (int)$roomId,
                userId: (int)\Auth::id(),
                nickname: (string)\Auth::user()->nickname,
                readyCount: (int)$readyCount,
                totalCount: (int)$totalCount,
                allReady: (bool)$allReady,
            ));
            
            return response()->json([
                'success' => true,
                'ready_count' => $readyCount,
                'total_count' => $totalCount,
                'all_ready' => $allReady,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getReadyCount($roomId)
    {
        try {
            $room = Room::findOrFail($roomId);
            
            $totalCount = RoomParticipant::where('room_id', $roomId)
                ->count();
                
            $readyCount = RoomParticipant::where('room_id', $roomId)
                ->where('is_ready', true)
                ->count();
            
            return response()->json([
                'ready_count' => $readyCount,
                'total_count' => $totalCount,
                'all_ready' => $readyCount >= $totalCount 
                               && $totalCount > 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'ready_count' => 0,
                'total_count' => 0,
                'all_ready' => false,
            ], 500);
        }
    }
}

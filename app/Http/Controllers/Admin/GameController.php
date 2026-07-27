<?php

namespace App\Http\Controllers\Admin;

use App\Events\AnswerReceived;
use App\Events\GameFinished;
use App\Events\QuestionEnded;
use App\Events\QuestionStarted;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Room;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GameController extends Controller
{
    public function show($roomId)
    {
        $room = Room::with(['quiz.questions.options', 'participants.user'])->findOrFail($roomId);
        
        if ($room->status !== 'ongoing') {
            return redirect()->route('admin.rooms.lobby', $roomId);
        }

        $currentQuestion = null;
        if ($room->current_question > 0) {
            $currentQuestion = $room->quiz->questions->where('order_number', $room->current_question)->first();
        }

        $votes = $this->calculateVoteCounts($room->id, $currentQuestion?->id);
        $scoreboard = $this->getScoreboard($room->id);

        return view('admin.game.show', compact('room', 'currentQuestion', 'votes', 'scoreboard'));
    }

    public function startFirstQuestion($roomId)
    {
        $room = Room::with('quiz.questions.options', 'quiz.questions.topic')->findOrFail($roomId);
        
        if ($room->status !== 'ongoing') {
            return response()->json(['error' => 'Game must be in progress'], 400);
        }

        if ($room->current_question !== 0) {
            return response()->json(['error' => 'Game already started'], 400);
        }

        $total = $room->quiz->questions->count();
        if ($total === 0) {
            return response()->json(['error' => 'No questions in this quiz'], 404);
        }

        $room->update([
            'current_question' => 1,
            'question_started_at' => now(),
        ]);
        
        $question = $room->quiz->questions->where('order_number', 1)->first();

        // Reset is_ready for all participants
        \App\Models\RoomParticipant::where('room_id', $room->id)->update(['is_ready' => false]);

        broadcast(new QuestionStarted(
            $room->id,
            $question,
            1,
            $total,
            $question->time_limit
        ))->toOthers();

        return response()->json([
            'success' => true,
            'question_number' => 1,
            'question' => $question
        ]);
    }

    public function nextQuestion($roomId)
    {
        $room = Room::with('quiz.questions.options', 'quiz.questions.topic')->findOrFail($roomId);
        
        $nextNum = $room->current_question + 1;
        $total = $room->quiz->questions->count();

        if ($nextNum > $total) {
            return response()->json(['success' => false, 'message' => 'No more questions']);
        }

        $room->update([
            'current_question' => $nextNum,
            'question_started_at' => now(),
        ]);
        
        $question = $room->quiz->questions->where('order_number', $nextNum)->first();

        // Reset is_ready if we want to use it between questions (not strictly required by user but good practice)
        \App\Models\RoomParticipant::where('room_id', $room->id)->update(['is_ready' => false]);

        broadcast(new QuestionStarted(
            $room->id,
            $question,
            $nextNum,
            $total,
            $question->time_limit
        ))->toOthers();

        return response()->json([
            'success' => true,
            'question_number' => $nextNum,
            'question' => $question
        ]);
    }

    public function endQuestion($roomId)
    {
        $room = Room::with('quiz.questions.options', 'participants')->findOrFail($roomId);
        $question = $room->quiz->questions->where('order_number', $room->current_question)->first();
        
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        $correctOption = $question->options->where('is_correct', true)->first();
        $startTime = $room->question_started_at;
        $timeLimit = $question->time_limit;

        // Calculate and update scores
        $answers = Answer::where('room_id', $room->id)
            ->where('question_id', $question->id)
            ->get();

        foreach ($answers as $answer) {
            $points = 0;
            if ($answer->is_correct) {
                // Scoring: base 1000 + bonus floor(900 * remaining / limit)
                $timeTaken = Carbon::parse($answer->answered_at)->diffInSeconds($startTime);
                $remaining = max(0, $timeLimit - $timeTaken);
                $bonus = floor(900 * ($remaining / $timeLimit));
                $points = 1000 + $bonus;
            }
            
            $answer->update(['points_earned' => $points]);

            $score = Score::where('room_id', $room->id)
                ->where('user_id', $answer->user_id)
                ->first();

            if ($score) {
                $score->increment('total_score', (int)$points);
            }
        }

        // Recalculate Ranks
        $scores = Score::where('room_id', $room->id)
            ->orderBy('total_score', 'desc')
            ->get();

        foreach ($scores as $index => $s) {
            $s->update(['rank' => $index + 1]);
        }

        $scoreboard = $this->getScoreboard($room->id, 10, true);

        broadcast(new QuestionEnded(
            $room->id,
            $correctOption->color,
            $correctOption->option_text,
            $scoreboard
        ))->toOthers();

        return response()->json([
            'success' => true,
            'scoreboard' => $scoreboard,
            'correct_color' => $correctOption->color
        ]);
    }

    public function endGame($roomId)
    {
        $room = Room::findOrFail($roomId);
        
        $room->update([
            'status' => 'finished',
            'ended_at' => now(),
        ]);

        $room->quiz->update(['status' => 'finished']);

        $finalScores = $this->getScoreboard($room->id, 20, true);

        broadcast(new GameFinished($room->id, $finalScores))->toOthers();

        return response()->json(['success' => true]);
    }

    public function getVoteCounts($roomId)
    {
        $room = Room::findOrFail($roomId);
        $question = $room->quiz->questions->where('order_number', $room->current_question)->first();
        
        return response()->json($this->calculateVoteCounts($room->id, $question?->id));
    }

    private function calculateVoteCounts($roomId, $questionId)
    {
        if (!$questionId) return ['red' => 0, 'blue' => 0, 'yellow' => 0, 'green' => 0, 'total' => 0];

        $counts = Answer::where('room_id', $roomId)
            ->where('answers.question_id', $questionId)
            ->join('options', 'answers.option_id', '=', 'options.id')
            ->selectRaw('options.color, count(*) as count')
            ->groupBy('options.color')
            ->pluck('count', 'color')
            ->toArray();

        $colors = ['red', 'blue', 'yellow', 'green'];
        $result = [];
        $total = 0;
        foreach ($colors as $color) {
            $result[$color] = $counts[$color] ?? 0;
            $total += $result[$color];
        }
        $result['total'] = $total;

        return $result;
    }

    private function getScoreboard($roomId, $limit = 10, $bust = false)
    {
        $cacheKey = "scoreboard.{$roomId}";
        
        if ($bust) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function() use ($roomId, $limit) {
            return Score::where('room_id', $roomId)
                ->join('users', 'scores.user_id', '=', 'users.id')
                ->orderBy('total_score', 'desc')
                ->take($limit)
                ->get(['scores.rank', 'users.nickname', 'scores.total_score'])
                ->toArray();
        });
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Score;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ResultsController extends Controller
{
    public function show($roomId)
    {
        Score::syncForRoom($roomId);

        $room = Room::with(['quiz.questions.options', 'scores.user', 'participants.user'])->findOrFail($roomId);
        
        $scores = Score::where('room_id', $roomId)
            ->with('user')
            ->orderBy('total_score', 'desc')
            ->get();

        // Stats per question
        $questionStats = [];
        foreach ($room->quiz->questions as $question) {
            $totalAnswers = Answer::where('room_id', $roomId)->where('question_id', $question->id)->count();
            $correctAnswers = Answer::where('room_id', $roomId)->where('question_id', $question->id)->where('is_correct', true)->count();
            
            $mostChosen = Answer::where('room_id', $roomId)
                ->where('answers.question_id', $question->id)
                ->join('options', 'answers.option_id', '=', 'options.id')
                ->selectRaw('options.color, options.option_text, count(*) as count')
                ->groupBy('options.color', 'options.option_text')
                ->orderBy('count', 'desc')
                ->first();

            $questionStats[] = [
                'question' => $question,
                'total' => $totalAnswers,
                'correct' => $correctAnswers,
                'accuracy' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100) : 0,
                'most_chosen' => $mostChosen
            ];
        }

        return view('admin.results.show', compact('room', 'scores', 'questionStats'));
    }

    public function export($roomId)
    {
        $room = Room::with('quiz')->findOrFail($roomId);
        $scores = Score::where('room_id', $roomId)
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->orderBy('total_score', 'desc')
            ->get(['scores.*', 'users.nickname', 'users.full_name']);

        $filename = "brainblitz-results-{$room->room_code}-" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Rank', 'Nickname', 'Full Name', 'Total Score', 'Correct Answers', 'Wrong Answers', 'Accuracy %'];

        $callback = function() use($scores, $columns, $roomId) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($scores as $score) {
                $correct = Answer::where('room_id', $roomId)->where('user_id', $score->user_id)->where('is_correct', true)->count();
                $total = Answer::where('room_id', $roomId)->where('user_id', $score->user_id)->count();
                $wrong = $total - $correct;
                $accuracy = $total > 0 ? round(($correct / $total) * 100) : 0;

                fputcsv($file, [
                    $score->rank,
                    $score->nickname,
                    $score->full_name,
                    $score->total_score,
                    $correct,
                    $wrong,
                    $accuracy . '%'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

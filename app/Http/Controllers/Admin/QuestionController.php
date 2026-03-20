<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function create($quizId)
    {
        $quiz = Quiz::with('topics')->findOrFail($quizId);
        
        // topics to pick from depends on topic_mode
        if ($quiz->topic_mode === 'random') {
            $topics = Topic::orderBy('name')->get();
        } else {
            $topics = $quiz->topics;
        }

        return view('admin.questions.create', compact('quiz', 'topics'));
    }

    public function store(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $validatedData = $request->validate([
            'question_text' => ['required', 'string', 'max:500'],
            'topic_id' => ['required', 'exists:topics,id'],
            'time_limit' => ['required', 'integer', 'min:10', 'max:120'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.text' => ['required', 'string', 'max:255'],
            'options.*.color' => ['required', 'in:red,blue,yellow,green'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
        ]);

        DB::transaction(function () use ($quiz, $validatedData) {
            $maxOrder = $quiz->questions()->max('order_number') ?? 0;

            $question = Question::create([
                'quiz_id' => $quiz->id,
                'topic_id' => $validatedData['topic_id'],
                'question_text' => $validatedData['question_text'],
                'order_number' => $maxOrder + 1,
                'time_limit' => $validatedData['time_limit'],
            ]);

            foreach ($validatedData['options'] as $index => $optionData) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'],
                    'color' => $optionData['color'],
                    'is_correct' => ($index == $validatedData['correct_option']),
                ]);
            }
        });

        return redirect()->route('admin.quizzes.show', $quizId)->with('success', 'Question added successfully!');
    }

    public function edit($quizId, $questionId)
    {
        $quiz = Quiz::with('topics')->findOrFail($quizId);
        $question = Question::with('options')->findOrFail($questionId);

        if ($quiz->topic_mode === 'random') {
            $topics = Topic::orderBy('name')->get();
        } else {
            $topics = $quiz->topics;
        }

        return view('admin.questions.edit', compact('quiz', 'question', 'topics'));
    }

    public function update(Request $request, $quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $validatedData = $request->validate([
            'question_text' => ['required', 'string', 'max:500'],
            'topic_id' => ['required', 'exists:topics,id'],
            'time_limit' => ['required', 'integer', 'min:10', 'max:120'],
            'options' => ['required', 'array', 'size:4'],
            'options.*.text' => ['required', 'string', 'max:255'],
            'options.*.color' => ['required', 'in:red,blue,yellow,green'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
        ]);

        DB::transaction(function () use ($question, $validatedData) {
            $question->update([
                'topic_id' => $validatedData['topic_id'],
                'question_text' => $validatedData['question_text'],
                'time_limit' => $validatedData['time_limit'],
            ]);

            // Simple way to handle options update
            $question->options()->delete();

            foreach ($validatedData['options'] as $index => $optionData) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'],
                    'color' => $optionData['color'],
                    'is_correct' => ($index == $validatedData['correct_option']),
                ]);
            }
        });

        return redirect()->route('admin.quizzes.show', $quizId)->with('success', 'Question updated successfully!');
    }

    public function destroy($quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);
        
        DB::transaction(function () use ($question, $quizId) {
            $orderDeleted = $question->order_number;
            $question->delete();

            // Re-order remaining questions
            Question::where('quiz_id', $quizId)
                ->where('order_number', '>', $orderDeleted)
                ->decrement('order_number');
        });

        return redirect()->route('admin.quizzes.show', $quizId)->with('success', 'Question deleted successfully!');
    }
    public function reorder(Request $request, $quizId, $questionId)
    {
        $question = Question::where('quiz_id', $quizId)->findOrFail($questionId);
        $direction = $request->input('direction'); // 'up' or 'down'

        DB::transaction(function () use ($question, $direction, $quizId) {
            if ($direction === 'up' && $question->order_number > 1) {
                // Find previous question
                $prevQuestion = Question::where('quiz_id', $quizId)
                    ->where('order_number', $question->order_number - 1)
                    ->first();
                
                if ($prevQuestion) {
                    $prevQuestion->update(['order_number' => $question->order_number]);
                    $question->update(['order_number' => $question->order_number - 1]);
                }
            } elseif ($direction === 'down') {
                $maxOrder = Question::where('quiz_id', $quizId)->max('order_number');
                if ($question->order_number < $maxOrder) {
                    // Find next question
                    $nextQuestion = Question::where('quiz_id', $quizId)
                        ->where('order_number', $question->order_number + 1)
                        ->first();
                    
                    if ($nextQuestion) {
                        $nextQuestion->update(['order_number' => $question->order_number]);
                        $question->update(['order_number' => $question->order_number + 1]);
                    }
                }
            }
        });

        return redirect()->route('admin.quizzes.show', $quizId)->with('success', 'Question order updated!');
    }
}

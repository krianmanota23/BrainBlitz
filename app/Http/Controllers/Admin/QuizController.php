<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('created_by', Auth::id())
            ->withCount('questions')
            ->with('latestRoom')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $topics = Topic::orderBy('name')->get();
        return view('admin.quizzes.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'topic_mode' => ['required', 'in:single,multiple,random'],
            'time_per_question' => ['required', 'integer', 'min:10', 'max:120'],
            'max_participants' => ['required', 'integer', 'min:2', 'max:100'],
            'topic_ids' => ['required_if:topic_mode,single,multiple', 'array'],
            'topic_ids.*' => ['exists:topics,id'],
        ]);

        $quiz = Quiz::create([
            'title' => $validatedData['title'],
            'room_code' => $this->generateUniqueRoomCode(),
            'created_by' => Auth::id(),
            'topic_mode' => $validatedData['topic_mode'],
            'time_per_question' => $validatedData['time_per_question'],
            'max_participants' => $validatedData['max_participants'],
            'status' => 'draft',
        ]);

        if ($request->has('topic_ids') && $validatedData['topic_mode'] !== 'random') {
            $quiz->topics()->attach($request->topic_ids);
        }

        return redirect()->route('admin.quizzes.show', $quiz->id)->with('success', 'Quiz created successfully!');
    }

    public function show($id)
    {
        $quiz = Quiz::with(['topics', 'questions.options', 'questions.topic'])->findOrFail($id);
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->status !== 'draft') {
            return back()->with('error', 'Only draft quizzes can be edited.');
        }

        $topics = Topic::orderBy('name')->get();
        return view('admin.quizzes.edit', compact('quiz', 'topics'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->status !== 'draft') {
            return back()->with('error', 'Only draft quizzes can be edited.');
        }

        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'topic_mode' => ['required', 'in:single,multiple,random'],
            'time_per_question' => ['required', 'integer', 'min:10', 'max:120'],
            'max_participants' => ['required', 'integer', 'min:2', 'max:100'],
            'topic_ids' => ['required_if:topic_mode,single,multiple', 'array'],
            'topic_ids.*' => ['exists:topics,id'],
        ]);

        $quiz->update([
            'title' => $validatedData['title'],
            'topic_mode' => $validatedData['topic_mode'],
            'time_per_question' => $validatedData['time_per_question'],
            'max_participants' => $validatedData['max_participants'],
        ]);

        if ($validatedData['topic_mode'] !== 'random') {
            $quiz->topics()->sync($request->topic_ids);
        } else {
            $quiz->topics()->detach();
        }

        return redirect()->route('admin.quizzes.show', $quiz->id)->with('success', 'Quiz updated successfully!');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->status !== 'draft') {
            return back()->with('error', 'Only draft quizzes can be deleted.');
        }

        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    private function generateUniqueRoomCode()
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Quiz::where('room_code', $code)->exists());

        return $code;
    }
}

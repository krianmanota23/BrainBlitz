<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::withCount('questions')->orderBy('name')->get();
        return view('admin.topics.index', compact('topics'));
    }

    public function create()
    {
        return view('admin.topics.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'unique:topics,name', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Topic::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully!');
    }

    public function edit(Topic $topic)
    {
        return view('admin.topics.edit', compact('topic'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'unique:topics,name,' . $topic->id, 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $topic->update($validatedData);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully!');
    }

    public function destroy(Topic $topic)
    {
        if ($topic->questions()->count() > 0) {
            return back()->with('error', 'Cannot delete topic with questions attached.');
        }

        $topic->delete();

        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted successfully!');
    }
}

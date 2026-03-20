<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'quizzes' => Quiz::where('created_by', Auth::id())->count(),
            'topics' => Topic::where('created_by', Auth::id())->count(),
            'games' => Room::whereHas('quiz', fn($q) => $q->where('created_by', Auth::id()))
                        ->where('status', 'finished')->count(),
            'students' => RoomParticipant::whereHas('room.quiz', fn($q) => $q->where('created_by', Auth::id()))
                        ->distinct('user_id')->count('user_id'),
        ];

        $recentQuizzes = Quiz::where('created_by', Auth::id())
            ->withCount('questions')
            ->latest()
            ->take(5)
            ->get();

        $activeRoom = Room::whereHas('quiz', fn($q) => $q->where('created_by', Auth::id()))
            ->whereIn('status', ['waiting', 'ongoing'])
            ->with(['quiz', 'participants'])
            ->first();

        return view('admin.dashboard', compact('stats', 'recentQuizzes', 'activeRoom'));
    }
}

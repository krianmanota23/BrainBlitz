<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $stats = null;
        if (Auth::user()->role === 'student') {
            $stats = [
                'games_played' => \App\Models\RoomParticipant::where('user_id', Auth::id())
                    ->whereHas('room', fn($q) => $q->where('status', 'finished'))->count(),
                'best_rank' => \App\Models\Score::where('user_id', Auth::id())->min('rank') ?? 'N/A',
                'total_score' => \App\Models\Score::where('user_id', Auth::id())->sum('total_score'),
                'accuracy' => $this->calculateOverallAccuracy(),
            ];
        }

        return view('auth.profile', [
            'user' => Auth::user(),
            'stats' => $stats
        ]);
    }

    private function calculateOverallAccuracy()
    {
        $totalAnswers = \App\Models\Answer::where('user_id', Auth::id())->count();
        if ($totalAnswers == 0) return 0;
        $correctAnswers = \App\Models\Answer::where('user_id', Auth::id())->where('is_correct', true)->count();
        return round(($correctAnswers / $totalAnswers) * 100);
    }

    public function updateNickname(Request $request)
    {
        $validatedData = $request->validate([
            'nickname' => ['required', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $user->nickname = $validatedData['nickname'];
        $user->save();

        return back()->with('success', 'Nickname updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = $request->password; // Hash is handled by the model's 'hashed' cast
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }
}

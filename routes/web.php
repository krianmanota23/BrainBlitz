<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/check-username', [AuthController::class, 'checkUsername'])->name('check.username');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\RoomController;

// Admin Routes
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Topic Routes
    Route::resource('topics', TopicController::class)->except(['show']);

    // Quiz Routes
    Route::resource('quizzes', QuizController::class);

    // Question Routes (nested under quiz)
    Route::get('/quizzes/{quizId}/questions/create', [QuestionController::class, 'create'])->name('quizzes.questions.create');
    Route::post('/quizzes/{quizId}/questions', [QuestionController::class, 'store'])->name('quizzes.questions.store');
    Route::get('/quizzes/{quizId}/questions/{id}/edit', [QuestionController::class, 'edit'])->name('quizzes.questions.edit');
    Route::put('/quizzes/{quizId}/questions/{id}', [QuestionController::class, 'update'])->name('quizzes.questions.update');
    Route::delete('/quizzes/{quizId}/questions/{id}', [QuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    Route::post('/quizzes/{quizId}/questions/{questionId}/reorder', [QuestionController::class, 'reorder'])->name('quizzes.questions.reorder');

    // Room Routes
    Route::post('/quizzes/{quizId}/launch', [RoomController::class, 'launch'])->name('quizzes.launch');
    Route::get('/rooms/{roomId}/lobby', [RoomController::class, 'lobby'])->name('rooms.lobby');
    Route::post('/rooms/{roomId}/start', [RoomController::class, 'startGame'])->name('rooms.start');
    Route::get('/rooms/{roomId}/participants', [RoomController::class, 'getParticipants'])->name('rooms.participants');
    
    // Active Game Routes (Phase 6)
    Route::get('/rooms/{roomId}/game', [\App\Http\Controllers\Admin\GameController::class, 'show'])->name('game.show');
    Route::post('/rooms/{roomId}/start-first-question', [\App\Http\Controllers\Admin\GameController::class, 'startFirstQuestion'])->name('rooms.start_first');
    Route::post('/rooms/{roomId}/next-question', [\App\Http\Controllers\Admin\GameController::class, 'nextQuestion'])->name('rooms.next');
    Route::post('/rooms/{roomId}/end-question', [\App\Http\Controllers\Admin\GameController::class, 'endQuestion'])->name('rooms.end_question');
    Route::post('/rooms/{roomId}/end-game', [\App\Http\Controllers\Admin\GameController::class, 'endGame'])->name('rooms.end_game');
    Route::get('/rooms/{roomId}/votes', [\App\Http\Controllers\Admin\GameController::class, 'getVoteCounts'])->name('rooms.votes');
    
    // Results Routes (Phase 7)
    Route::get('/rooms/{roomId}/results', [\App\Http\Controllers\Admin\ResultsController::class, 'show'])->name('rooms.results');
    Route::get('/rooms/{roomId}/results/export', [\App\Http\Controllers\Admin\ResultsController::class, 'export'])->name('rooms.results.export');

});

use App\Http\Controllers\Student\GameController; // Added

// Student Routes
Route::middleware(['student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [GameController::class, 'dashboard'])->name('dashboard');

    Route::get('/join', [GameController::class, 'showJoinForm'])->name('join');
    Route::post('/join', [GameController::class, 'joinRoom'])->name('join.room');
    Route::get('/rooms/{roomId}/waiting', [GameController::class, 'waitingRoom'])->name('rooms.waiting');
    Route::get('/rooms/{roomId}/status', [GameController::class, 'checkRoomStatus'])->name('rooms.status');

    // Active Game Routes (Phase 6)
    Route::get('/rooms/{roomId}/game', [GameController::class, 'game'])->name('game');
    Route::post('/rooms/{roomId}/answer', [GameController::class, 'submitAnswer'])->name('rooms.submit_answer');

    // Results Route (Phase 7)
    Route::get('/rooms/{roomId}/results', [\App\Http\Controllers\Student\ResultsController::class, 'show'])->name('rooms.results');

    // Ready System Routes
    Route::post('/rooms/{roomId}/ready', [GameController::class, 'markReady'])->name('ready');
    Route::get('/rooms/{roomId}/ready-count', [GameController::class, 'getReadyCount'])->name('ready.count');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::post('/profile/nickname', [ProfileController::class, 'updateNickname'])->name('profile.nickname');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// TV Display Routes (Public Audience Views)
Route::prefix('tv')->name('tv.')->group(function () {
    Route::get('/{roomId}/lobby', [\App\Http\Controllers\Admin\TvController::class, 'lobby'])->name('lobby');
    Route::get('/{roomId}/game', [\App\Http\Controllers\Admin\TvController::class, 'game'])->name('game');
    Route::get('/{roomId}/results', [\App\Http\Controllers\Admin\TvController::class, 'results'])->name('results');
});

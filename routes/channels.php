<?php

use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('room.{roomId}', function($user, $roomId) {
    if (!$user) return false;

    $room = Room::findOrFail($roomId);
    $quiz = $room->quiz;

    // Admin authorization
    if ($user->role === 'admin') {
        return [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'role' => 'admin',
            'is_ready' => false
        ];
    }

    // Student authorization
    $participant = RoomParticipant::where('room_id', $roomId)
        ->where('user_id', $user->id)
        ->first();

    if ($participant) {
        return [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'role' => 'student',
            'is_ready' => (bool)$participant->is_ready
        ];
    }

    return false;
});

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = ['room_id', 'user_id', 'total_score', 'rank'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function syncForRoom($roomId): void
    {
        $participants = RoomParticipant::where('room_id', $roomId)->get();
        foreach ($participants as $p) {
            self::firstOrCreate([
                'room_id' => $roomId,
                'user_id' => $p->user_id,
            ], [
                'total_score' => 0,
                'rank' => 0,
            ]);
        }

        foreach ($participants as $p) {
            $answers = Answer::where('room_id', $roomId)->where('user_id', $p->user_id)->get();
            $total = 0;
            foreach ($answers as $ans) {
                if ($ans->is_correct) {
                    $points = $ans->points_earned > 0 ? $ans->points_earned : 1000;
                    $total += $points;
                    if ($ans->points_earned == 0) {
                        $ans->update(['points_earned' => $points]);
                    }
                }
            }
            self::where('room_id', $roomId)->where('user_id', $p->user_id)->update(['total_score' => $total]);
        }

        $scores = self::where('room_id', $roomId)->orderBy('total_score', 'desc')->get();
        foreach ($scores as $index => $score) {
            $score->update(['rank' => $index + 1]);
        }
    }
}

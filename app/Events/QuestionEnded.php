<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $roomId,
        public string $correctColor,
        public string $correctOptionText,
        public array $scoreboard
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('room.' . $this->roomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'QuestionEnded';
    }

    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->roomId,
            'correct_color' => $this->correctColor,
            'correct_option_text' => $this->correctOptionText,
            'scoreboard' => $this->scoreboard,
        ];
    }
}

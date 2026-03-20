<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $roomId,
        public $question,
        public int $questionNumber,
        public int $totalQuestions,
        public int $timeLimit
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('room.' . $this->roomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'QuestionStarted';
    }

    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->roomId,
            'question' => [
                'id' => $this->question->id,
                'question_text' => $this->question->question_text,
                'topic' => ['name' => $this->question->topic->name ?? 'ARENA CLASSIC'],
                'options' => $this->question->options->map(fn($o) => [
                    'id' => $o->id,
                    'option_text' => $o->option_text,
                    'color' => $o->color,
                ]),
            ],
            'question_number' => $this->questionNumber,
            'total_questions' => $this->totalQuestions,
            'time_limit' => $this->timeLimit,
        ];
    }
}

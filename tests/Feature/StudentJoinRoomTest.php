<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\Room;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentJoinRoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_join_active_waiting_room_even_with_pasted_spaces(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $topic = Topic::create([
            'name' => 'Math',
            'slug' => 'math',
            'description' => 'Math topic',
            'created_by' => $admin->id,
        ]);

        $quiz = Quiz::create([
            'title' => 'MATH BLITZ',
            'room_code' => 'HNYVIR',
            'created_by' => $admin->id,
            'topic_mode' => 'single',
            'time_per_question' => 30,
            'max_participants' => 10,
            'status' => 'waiting',
        ]);
        $quiz->questions()->create([
            'question_text' => 'What is 2+2?',
            'topic_id' => $topic->id,
            'order_number' => 1,
            'time_limit' => 30,
        ]);

        $room = Room::create([
            'quiz_id' => $quiz->id,
            'room_code' => 'HNYVIR',
            'status' => 'waiting',
            'current_question' => 0,
        ]);

        // Student submits pasted code with leading/trailing spaces & lowercases
        $response = $this->actingAs($student)->post('/student/join', [
            'room_code' => ' hnyvir ',
        ]);

        $response->assertRedirect(route('student.rooms.waiting', $room->id));
        $this->assertDatabaseHas('room_participants', [
            'room_id' => $room->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_student_gets_clear_error_if_quiz_not_launched_yet(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $quiz = Quiz::create([
            'title' => 'UNLAUNCHED QUIZ',
            'room_code' => 'DRAFT1',
            'created_by' => $admin->id,
            'topic_mode' => 'random',
            'time_per_question' => 30,
            'max_participants' => 10,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($student)->post('/student/join', [
            'room_code' => 'DRAFT1',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('has not been launched by the host yet', session('error'));
    }
}

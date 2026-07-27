<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\Room;
use App\Models\RoomParticipant;
use App\Models\Score;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameLaunchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_start_game_for_waiting_room_without_sql_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $player1 = User::factory()->create(['role' => 'student']);
        $player2 = User::factory()->create(['role' => 'student']);

        $quiz = Quiz::create([
            'title' => 'TEST ARENA',
            'room_code' => 'ARENA1',
            'created_by' => $admin->id,
            'topic_mode' => 'random',
            'time_per_question' => 30,
            'max_participants' => 10,
            'status' => 'waiting',
        ]);

        $room = Room::create([
            'quiz_id' => $quiz->id,
            'room_code' => 'ARENA1',
            'status' => 'waiting',
            'current_question' => 0,
        ]);

        RoomParticipant::create(['room_id' => $room->id, 'user_id' => $player1->id, 'joined_at' => now()]);
        RoomParticipant::create(['room_id' => $room->id, 'user_id' => $player2->id, 'joined_at' => now()]);

        $response = $this->actingAs($admin)->post("/admin/rooms/{$room->id}/start");

        $response->assertRedirect(route('admin.game.show', $room->id));

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => 'ongoing',
        ]);

        $this->assertDatabaseHas('scores', [
            'room_id' => $room->id,
            'user_id' => $player1->id,
            'total_score' => 0,
        ]);

        $this->assertDatabaseHas('scores', [
            'room_id' => $room->id,
            'user_id' => $player2->id,
            'total_score' => 0,
        ]);
    }

    public function test_admin_can_view_battle_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/history');

        $response->assertStatus(200);
        $response->assertSee('ARENA BATTLE');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_single_topic_quiz(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $topic = Topic::create([
            'name' => 'General Science',
            'slug' => 'general-science',
            'description' => 'Science questions',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/quizzes', [
            'title' => 'SCIENCE BLITZ 2026',
            'topic_mode' => 'single',
            'topic_ids' => [$topic->id],
            'time_per_question' => 30,
            'max_participants' => 20,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'title' => 'SCIENCE BLITZ 2026',
            'topic_mode' => 'single',
            'created_by' => $admin->id,
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_create_a_randomized_mode_quiz(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/quizzes', [
            'title' => 'RANDOM CHAOS ARENA',
            'topic_mode' => 'random',
            'time_per_question' => 15,
            'max_participants' => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'title' => 'RANDOM CHAOS ARENA',
            'topic_mode' => 'random',
        ]);
    }
}

<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
        $this->category = Category::factory()->create();
    }

    public function test_user_can_get_their_tasks()
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        // Create tasks for another user
        Task::factory()->count(2)->create([
            'user_id' => User::factory()->create()->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data'); // Assuming pagination
    }

    public function test_user_can_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => '2024-12-31',
            'category_id' => $this->category->id
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                ->assertJson(['title' => 'Test Task']);

        $this->assertDatabaseHas('tasks', $taskData + ['user_id' => $this->user->id]);
    }

    public function test_user_cannot_create_task_with_invalid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', [
            'title' => '',
            'status' => 'invalid-status'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'status', 'description', 'due_date']);
    }

    public function test_user_can_update_their_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'status' => 'in-progress',
            'due_date' => '2024-12-31',
            'category_id' => $this->category->id
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson(['title' => 'Updated Task']);

        $this->assertDatabaseHas('tasks', $updateData + ['user_id' => $this->user->id]);
    }

    public function test_user_cannot_update_others_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_can_filter_tasks_by_status()
    {
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?status=completed');

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data');
    }

    public function test_user_can_search_tasks()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Search for this task'
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Another task'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?search=Search');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['title' => 'Search for this task']);
    }
}

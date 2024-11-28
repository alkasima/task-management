<?php

namespace Tests\Feature\API;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
    }

    public function test_user_can_get_all_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/categories');

        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_user_can_create_category()
    {
        $categoryData = ['name' => 'Test Category'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
                ->assertJson(['name' => 'Test Category']);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_user_cannot_create_category_with_invalid_data()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/categories', ['name' => '']);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_update_category()
    {
        $category = Category::factory()->create();
        $updateData = ['name' => 'Updated Category'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson(['name' => 'Updated Category']);

        $this->assertDatabaseHas('categories', $updateData);
    }

    public function test_user_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

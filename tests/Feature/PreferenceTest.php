<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Preference;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('Test Token')->plainTextToken;
    }

    public function test_user_can_set_preferences()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/preferences', [
            'sources' => ['BBC', 'CNN'],
            'categories' => ['Technology', 'Health'],
            'authors' => ['John Doe', 'Jane Doe'],
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Preferences updated successfully']);
    }

    public function test_user_can_get_preferences()
    {
        Preference::create([
            'user_id' => $this->user->id,
            'sources' => ['BBC'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/preferences');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'user_id',
                    'sources',
                    'categories',
                    'authors',
                    'created_at',
                    'updated_at',
                ]);
    }
}

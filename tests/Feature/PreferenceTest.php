<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Preference;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_set_preferences()
    {
        $user = User::factory()->create();

        $token = $user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
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
        $user = User::factory()->create();
        $preference = Preference::create([
            'user_id' => $user->id,
            'sources' => ['BBC'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        $token = $user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/preferences');

        $response->assertStatus(200)
                 ->assertJson([
                     'sources' => ['BBC'],
                     'categories' => ['Technology'],
                     'authors' => ['John Doe'],
                 ]);
    }
}

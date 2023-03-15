<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class EditProfileTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForEditProfile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'name' => 'Test User 2',
            'email' => 'testuser2gmail.com', //Wrong email format
        ];

        $response = $this->post('/api/edit/profile', $postData, ['Accept' => 'application/json']);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertTrue(isset($responseArray['errors']));
        $this->assertTrue(isset($responseArray['message']));

        //This test would also run correctly if an existing email is passed
    }

    public function testEditProfile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail()
        ];

        $response = $this->post('/api/edit/profile', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

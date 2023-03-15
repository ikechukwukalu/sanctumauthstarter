<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class ForgotPasswordTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForForgotPassword()
    {
        $postData = [
            'email' => '0000000000.com', //Wrong email format
        ];

        $response = $this->post('/api/auth/forgot/password', $postData, ['Accept' => 'application/json']);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertTrue(isset($responseArray['errors']));
        $this->assertTrue(isset($responseArray['message']));
    }

    public function testForgotPassword()
    {
        $postData = [
            'email' => $this->faker->unique()->safeEmail(), //Email doesn't exist
        ];

        $user = User::factory()->create([
                'email' => $postData['email']
        ]);

        $response = $this->post('/api/auth/forgot/password', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

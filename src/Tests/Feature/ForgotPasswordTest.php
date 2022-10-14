<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

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
            'email' => '0000000000@gmail.com', //Email doesn't exist
        ];

        $response = $this->post('/api/auth/forgot/password', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(500, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);
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

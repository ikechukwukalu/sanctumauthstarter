<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class ResetPasswordTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testValidUrlForForgotPassword()
    {
        $response = $this->get('/forgot/password');

        $response->assertStatus(200);
    }

    public function testErrorValidationForResetPassword()
    {
        $postData = [
            'email' => 'testuser2gmail.com', //Wrong email format
            'password' => 'password',
            'password_confirmation' => '1234567', //None matching passwords
        ];

        $response = $this->post('/api/auth/reset/password', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(500, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);

        //This test would also run correctly if an existing email is passed
    }

    public function testResetPassword()
    {
        $user =  User::first();

        if (!isset($user->id)) {
            $user = User::create([
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => "{_'hhtl[N#%H3BXe"
            ]);
        }

        $postData = [
            'email' => $user->email,
            'password' => '$Ty12345678',
            'password_confirmation' => '$Ty12345678'
        ];

        $response = $this->post('/api/auth/reset/password', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForRegistration()
    {
        $postData = [
            'name' => 'Test User 2',
            'email' => 'testuser2gmail.com', //Wrong email format
            'password' => 'password',
            'password_confirmation' => '1234567' //None matching passwords
        ];

        $response = $this->post('/api/auth/register', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(500, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);

        //This test would also run correctly if an existing email is passed
    }

    public function testRegistration()
    {
        $random = Str::random(40);
        $postData = [
            'name' => 'Test User ' . $random,
            'email' => 'test-' . $random . '-user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post('/api/auth/register', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

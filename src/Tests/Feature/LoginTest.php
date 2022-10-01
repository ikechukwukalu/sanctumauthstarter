<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForLogin()
    {
        $postData = [
            'email' => 'testuser2gmail.com', //Wrong email format
            'password' => '' //Empty passwords
        ];

        $response = $this->post('/api/auth/login', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(500, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);
    }

    public function testLoginThrottling()
    {
        $random = Str::random(40);
        $postData = [
            'email' => 'testuser1@gmail.com',
            'password' => 'password'
        ];

        $user =  User::firstOrCreate(
            ['email' => $postData['email']],
            [
                'name' => $random,
                'password' => Hash::make('12345678')
            ]
        );

        //Multipe login attempts
        for($i = 1; $i <= 6; $i ++) {
            $response = $this->post('/api/auth/login', $postData);
        }

        $response->assertStatus(302);
    }

    public function testLogin()
    {
        $random = Str::random(40);
        $postData = [
            'email' => 'testuser1@gmail.com',
            'password' => 'password'
        ];

        $user =  User::firstOrCreate(
            ['email' => $postData['email']],
            [
                'name' => $random,
                'password' => Hash::make('12345678')
            ]
        );

        $response = $this->post('/api/auth/login', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

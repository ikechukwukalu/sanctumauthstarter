<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\User;

class LoginTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForLogin()
    {
        $postData = [
            'email' => $this->faker->unique()->safeEmail(), //Wrong email format
            'password' => '' //Empty passwords
        ];

        $response = $this->post('/api/auth/login', $postData);
        $response->assertStatus(302);
    }

    public function testLoginThrottling()
    {
        $postData = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe")
        ];

        $user = User::factory()->create([
            'email' => $postData['email']
        ]);

        //Multipe login attempts
        for($i = 1; $i <= 6; $i ++) {
            $response = $this->post('/api/auth/login', $postData);
        }

        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(400, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);
    }

    public function testLogin()
    {
        $postData = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe")
        ];

        $user = User::factory()->create([
            'email' => $postData['email'],
            'password' => Hash::make($postData['password'])
        ]);

        $response = $this->post('/api/auth/login', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

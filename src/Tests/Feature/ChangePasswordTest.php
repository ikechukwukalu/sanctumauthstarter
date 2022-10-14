<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class ChangePasswordTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForChangePassword()
    {
        $user =  User::find(1);

        if (!isset($user->id)) {
            $user = User::create([
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => Hash::make("{_'hhtl[N#%H3BXe")
            ]);
        }

        $this->actingAs($user);

        $postData = [
            'current_password' => '12345678', //Wrong current password
            'password' => 'password', //Wrong password format
            'password_confirmation' => '1234567' //None matching passwords
        ];

        $response = $this->post('/api/change/password', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(500, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);
    }

    public function testChangePassword()
    {
        $userData = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe")
        ];

        $user =  User::create([
            'name' => $this->faker->name(),
            'email' => $userData['email'],
            'password' => Hash::make($userData['password'])
        ]);

        $this->actingAs($user);

        $postData = [
            'current_password' => "{_'hhtl[N#%H3BXe",
            'password' => '$Ty12345678',
            'password_confirmation' => '$Ty12345678'
        ];

        $response = $this->post('/api/change/password', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }
}

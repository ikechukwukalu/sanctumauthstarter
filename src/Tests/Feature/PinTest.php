<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use Ikechukwukalu\Sanctumauthstarter\Models\Book;

class PinTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testErrorValidationForChangePin()
    {
        $user =  User::find(1);

        if (!isset($user->id)) {
            $user = User::create([
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => Hash::make("{_'hhtl[N#%H3BXe")
            ]); // Would still have the default pin
        }

        $this->actingAs($user);

        $postData = [
            'current_pin' => '9090', //Wrong current pin
            'pin' => '1uu4', //Wrong pin format
            'pin_confirmation' => '1234' //None matching pins
        ];

        $response = $this->post('/api/change/pin', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(500, $responseArray['status_code']);
        $this->assertEquals('fail', $responseArray['status']);
    }

    public function testChangePin()
    {
        $userData = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe")
        ];

        $user =  User::create([
            'name' => $this->faker->name(),
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'pin' => Hash::make(config('sanctumauthstarter.pin.default', '0000')),
        ]);

        $this->actingAs($user);

        $postData = [
            'current_pin' => config('sanctumauthstarter.pin.default', '0000'),
            'pin' => '1234',
            'pin_confirmation' => '1234'
        ];

        $this->assertTrue(Hash::check($postData['current_pin'], $user->pin));

        $response = $this->post('/api/change/pin', $postData);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }

    public function testRequirePinMiddleWareForCreateBook()
    {
        $pin = 1234;

        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe"),
            'pin' => Hash::make($pin),
            'default_pin' => 0
        ]);

        $this->assertTrue(Hash::check($pin, $user->pin));

        $this->actingAs($user);

        if (Route::has('createBookTest')) {
            $postData = [
                'name' => $this->faker->sentence(rand(1,5)),
                'isbn' => $this->faker->unique()->isbn13(),
                'authors' => implode(",", [$this->faker->name(), $this->faker->name()]),
                'publisher' => $this->faker->name(),
                'number_of_pages' => rand(45,1500),
                'country' => $this->faker->countryISOAlpha3(),
                'release_date' => date('Y-m-d')
            ];

            $response = $this->json('POST', '/api/v1/books', $postData);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);
            $this->assertTrue(isset($responseArray['data']['url']));

            $postData = [
                config('sanctumauthstarter.pin.input', '_pin') => (string) $pin
            ];
            $url = $responseArray['data']['url'];

            $response = $this->post($url, $postData);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);

        } else {
            $this->assertTrue(true);
        }

    }

    public function testRequirePinMiddleWareForUpdateBook()
    {
        $pin = 1234;

        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe"),
            'pin' => Hash::make($pin),
            'default_pin' => 0
        ]);

        $this->assertTrue(Hash::check($pin, $user->pin));

        $this->actingAs($user);

        if (Route::has('updateBookTest')) {
            $book = Book::find(1);

            if (!isset($book->id)) {
                $book = Book::create([
                        'name' => $this->faker->sentence(rand(1,5)),
                        'isbn' => $this->faker->unique()->isbn13(),
                        'authors' => implode(",", [$this->faker->name(), $this->faker->name()]),
                        'publisher' => $this->faker->name(),
                        'number_of_pages' => rand(45,1500),
                        'country' => $this->faker->countryISOAlpha3(),
                        'release_date' => date('Y-m-d')
                ]);
            }

            $id = $book->id;

            $postData = [
                'name' => $this->faker->sentence(rand(1,5)),
                'isbn' => $this->faker->unique()->isbn13(),
                'authors' => implode(",", [$this->faker->name(), $this->faker->name()]),
                'publisher' => $this->faker->name(),
                'number_of_pages' => rand(45,1500),
                'country' => $this->faker->countryISOAlpha3(),
                'release_date' => date('Y-m-d')
            ];

            $response = $this->json('PATCH', "/api/v1/books/{$id}", $postData);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);
            $this->assertTrue(isset($responseArray['data']['url']));

            $postData = [
                config('sanctumauthstarter.pin.input', '_pin') => (string) $pin
            ];
            $url = $responseArray['data']['url'];

            $response = $this->post($url, $postData);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);

        } else {
            $this->assertTrue(true);
        }

    }

    public function testRequirePinMiddleWareForDeleteBook()
    {
        $pin = 1234;

        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make("{_'hhtl[N#%H3BXe"),
            'pin' => Hash::make($pin),
            'default_pin' => 0
        ]);

        $this->assertTrue(Hash::check($pin, $user->pin));

        $this->actingAs($user);

        if (Route::has('deleteBookTest')) {
            $book = Book::find(1);

            if (!isset($book->id)) {
                $book = Book::create([
                    'name' => $this->faker->sentence(rand(1,5)),
                    'isbn' => $this->faker->unique()->isbn13(),
                    'authors' => implode(",", [$this->faker->name(), $this->faker->name()]),
                    'publisher' => $this->faker->name(),
                    'number_of_pages' => rand(45,1500),
                    'country' => $this->faker->countryISOAlpha3(),
                    'release_date' => date('Y-m-d')
                ]);
            }

            $id = $book->id;

            $response = $this->json('DELETE', "/api/v1/books/{$id}");
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);
            $this->assertTrue(isset($responseArray['data']['url']));

            $postData = [
                config('sanctumauthstarter.pin.input', '_pin') => (string) $pin
            ];
            $url = $responseArray['data']['url'];

            $response = $this->post($url, $postData);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);

        } else {
            $this->assertTrue(true);
        }

    }
}

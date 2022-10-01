<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

use App\Models\User;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $maxAttempts = 5; // change to the max attempt you want.
    protected $delayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function login(Request $request): JsonResponse
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $data = ["message" => 'Too many login attempts. Please try again in a minutes time.'];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $credentials = Validator::make($request->all(), [
           'email' => ['required', 'email', 'max:200'],
           'password' => ['required', 'string']
        ]);

        if ($credentials->fails()) {
            $this->incrementLoginAttempts($request);

            $data = (array) $credentials->messages();
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $remember = isset($request->remember_me) ? true : false;

        if (Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ], $remember))
        {
            $this->clearLoginAttempts($request);

            $user = Auth::user();
            $token = $user->createToken($request->email);

            $data = [
                'access_token' => $token->plainTextToken,
                'message' => 'Login successful'
            ];
            return $this->httpJsonResponse('success', 200, $data);
        }

        $this->incrementLoginAttempts($request);

        $data = ['message' => 'Wrong email or password'];
        return $this->httpJsonResponse('fail', 500, $data);
    }
}

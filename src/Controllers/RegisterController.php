<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Ikechukwukalu\Sanctumauthstarter\Notifications\WelcomeUser;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)->letters()
                                            ->mixedCase()->numbers()
                                            ->symbols()->uncompromised(),
                                        'confirmed'],
        ]);

        if ($validator->fails()) {
            $data = (array) $validator->messages();
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        $user =  User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password)
        ]);

        $data = [];

        if (config('sanctumauthstarter.registration.autologin', false)) {
            Auth::login($user);
            $token = $user->createToken($request->email);
            $data['access_token'] = $token->plainTextToken;
        }

        if (config('sanctumauthstarter.registration.notify.welcome', true)) {
            //Welcome email
            $user->notify(new WelcomeUser($user));
        }

        if (config('sanctumauthstarter.registration.notify.verify', true)) {
            $user->sendEmailVerificationNotification();
        }

        $data['message'] = trans('sanctumauthstarter::register.success');

        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}

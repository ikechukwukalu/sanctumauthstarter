<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $credentials = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:150', 'exists:users'
            ]
        ]);

        if ($credentials->fails()) {
            $data = ["message" => (array) $credentials->messages()];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        Password::sendResetLink(['email' => $request->email]);

        $data = ['message' => 'We\'ve sent you an email to reset your password'];
        return $this->httpJsonResponse('success', 200, $data);
    }
}

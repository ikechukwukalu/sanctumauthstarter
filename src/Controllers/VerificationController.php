<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class VerificationController extends Controller
{
    public function verifyUserEmail(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            $data = ['message' => 'URL invalid'];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $user = User::find($request->id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $data = ['message' => 'User has been verified'];
        return $this->httpJsonResponse('success', 200, $data);
    }

    public function resendUserEmailVerification(): JsonResponse
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $data = ['message' => 'Email verified'];
            return $this->httpJsonResponse('fail', 500, $data);
        }

        $user->sendEmailVerificationNotification();

        $data = ['message' => 'Email verification link has been sent to your email address'];
        return $this->httpJsonResponse('success', 200, $data);
    }
}

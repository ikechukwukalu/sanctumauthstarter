<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Ikechukwukalu\Sanctumauthstarter\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    //
    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();

        $data = [
            'access_token' => null,
            'message' => trans('sanctumauthstarter::auth.logout')
        ];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }
}

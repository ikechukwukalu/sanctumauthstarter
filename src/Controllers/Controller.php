<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use hisorange\BrowserDetect\Parser as Browser;
use Stevebauman\Location\Facades\Location;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, AuthenticatesUsers;

    protected $maxAttempts = 5; // change to the max attempt you want.
    protected $delayMinutes = 1;

    protected function httpJsonResponse(string $status, int $status_code, $data): JsonResponse
    {
        return Response::json([
            'status' => $status,
            'status_code' => $status_code,
            'data' => $data
        ]);
    }

    protected function hasTooManyAttempts (Request $request)
    {
        return $this->hasTooManyLoginAttempts($request);
    }

    protected function incrementAttempts (Request $request)
    {
        return $this->incrementLoginAttempts($request);
    }

    protected function clearAttempts (Request $request)
    {
        return $this->clearLoginAttempts($request);
    }

    protected function _fireLockoutEvent (Request $request)
    {
        return $this->fireLockoutEvent($request);
    }

    protected function _limiter ()
    {
        return $this->limiter();
    }

    protected function _throttleKey (Request $request)
    {
        return $this->throttleKey($request);
    }

    public function getClientIp(Request $request) {
        if ($position = Location::get()) {
            return $position->ip;
        }

        $server_keys = [
                        'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR',
                        'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
                        'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                        'REMOTE_ADDR'
                    ];

        foreach ($server_keys as $key){
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE |
                        FILTER_FLAG_NO_RES_RANGE) !== false
                    ) {
                        return $ip;
                    }
                }
            }
        }

        return $request->ip(); // it will return server ip when no client ip found
    }

    protected function getLoginUserInformation(): array
    {
        $info = [];

        if (Browser::deviceType() === 'Mobile' ||
            Browser::deviceType() === 'Tablet') {
            $info = [
                Browser::deviceFamily(),
                Browser::deviceModel()
            ];
        }

        if (Browser::deviceType() === 'Desktop') {
            $info = [
                Browser::browserName(),
                Browser::platformName()
            ];
        }

        if (Browser::deviceType() === 'Bot') {
            $info = [
                Browser::userAgent()
            ];
        }

        if ($position = Location::get()) {
            $info[] = $position->countryName;
            $info[] = $position->regionName;
            $info[] = $position->cityName;
        }

        return $info;
    }
}

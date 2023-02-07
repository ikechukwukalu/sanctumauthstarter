<?php

namespace Ikechukwukalu\Sanctumauthstarter\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use hisorange\BrowserDetect\Parser as Browser;
use Stevebauman\Location\Facades\Location;
use Illuminate\Http\Request;

trait Helpers {

    public function httpJsonResponse(string $status, int $status_code, $data): JsonResponse
    {
        return Response::json([
            'status' => $status,
            'status_code' => $status_code,
            'data' => $data
        ]);
    }

    public function getUserIp(Request $request) {
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

    public function getLoginUserInformation(): array
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

    public function pinRequestTerminated(): JsonResponse
    {
        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.fail'), 401,
            [
                'message' => trans('sanctumauthstarter::pin.terminated')
            ]
        );
    }

    public function pinValidationURL(string $url, null|string $redirect): JsonResponse
    {
        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.success'), 200,
            [
                'message' => trans('sanctumauthstarter::pin.require_pin'),
                'url' => $url,
                'redirect' => $redirect
            ]
        );
    }

    public function unknownErrorResponse(): JsonResponse
    {
        $data = ['message' =>
        trans('sanctumauthstarter::general.unknown_error')];

        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.fail'), 422, $data);
    }

    public function generateSalt(int $length = 9, bool $add_dashes = false, string $available_sets = 'luds'): string
    {
        $sets = [];
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $salt = '';
        foreach($sets as $set)
        {
            $salt .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $salt .= $all[array_rand($all)];

        $salt = str_shuffle($salt);

        if(!$add_dashes)
            return $salt;

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($salt) > $dash_len)
        {
            $dash_str .= substr($salt, 0, $dash_len) . '-';
            $salt = substr($salt, $dash_len);
        }
        $dash_str .= $salt;
        return $dash_str;
    }

}

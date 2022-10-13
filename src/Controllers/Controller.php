<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function httpJsonResponse(string $status, int $status_code, $data): JsonResponse
    {
        return Response::json([
            'status' => $status,
            'status_code' => $status_code,
            'data' => $data
        ]);
    }

    public function getClientIp(Request $request){
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
}

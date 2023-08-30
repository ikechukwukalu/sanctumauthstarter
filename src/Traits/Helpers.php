<?php

namespace Ikechukwukalu\Sanctumauthstarter\Traits;

use App\Models\User;
use App\Services\Auth\ThrottleRequestsService;
use Carbon\Carbon;
use Ikechukwukalu\Sanctumauthstarter\Notifications\UserLogin;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Stevebauman\Location\Facades\Location;

trait Helpers {

    public ThrottleRequestsService $throttleRequestsService;

    public function __construct(ThrottleRequestsService $throttleRequestsService)
    {
        $this->throttleRequestsService = $throttleRequestsService;
    }

    public function httpJsonResponse(string $status, int $status_code, $data): JsonResponse
    {
        return Response::json([
            'status' => $status,
            'status_code' => $status_code,
            'data' => $data
        ], $status_code);
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

    public function getUserLoginInformation(): array
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

    public function unknownErrorResponse(): JsonResponse
    {
        $data = ['message' =>
        trans('sanctumauthstarter::general.unknown_error')];

        return $this->httpJsonResponse(
            trans('sanctumauthstarter::general.fail'), 422, $data);
    }

    public function generateSalt(int $length = 9, bool $add_dashes = false, string $available_sets = 'luds'): string
    {
        [$sets, $salt, $all] = $this->saltsAllVar($available_sets);

        for($i = 0; $i < $length - count($sets); $i++) {
            $salt .= $all[array_rand($all)];
        }

        $salt = str_shuffle($salt);
        if (!$add_dashes) {
            return $salt;
        }

        return $this->addDashes($salt, $length);
    }

    public function requestAttempts(Request $request, string $trans = 'sanctumauthstarter::auth.throttle'): ?array
    {
        if ($this->throttleRequestsService->hasTooManyAttempts($request)) {
            $this->throttleRequestsService->_fireLockoutEvent($request);

            return ["message" => trans($trans,
                        ['seconds' =>
                            $this->throttleRequestsService->_limiter()
                            ->availableIn(
                                    $this->throttleRequestsService
                                        ->_throttleKey($request)
                                )
                        ])
                    ];
        }

        $this->throttleRequestsService->incrementAttempts($request);

        return null;
    }

    public function returnTwoFactorLoginView(array $data = []): View
    {
        $data['input'] = '2fa_code';

        if (!array_key_exists("message", $data)) {
            $data['message'] = trans('sanctumauthstarter::auth.failed');
        }

        return view('two-factor::login', $data);
    }

    public function userLoginNotification(User $user): void
    {
        if (config('sanctumauthstarter.login.notify.user', true)) {
            $time = Carbon::now()->isoFormat('Do of MMMM YYYY, h:mm:ssa');
            $deviceAndLocation = $this->getUserLoginInformation();
            $user->notify(new UserLogin($time, $deviceAndLocation));
        }
    }

    private function stringSets(string $available_sets): array
    {
        $sets = [];

        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }

        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }

        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }

        if (strpos($available_sets, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }

        return $sets;
    }

    private function saltsAllVar(string $available_sets): array
    {
        $sets = $this->stringSets($available_sets);
        $salt = '';
        $all = '';

        foreach ($sets as $set)
        {
            $salt .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        return [$sets, $salt, str_split($all)];
    }

    private function addDashes(string $salt, int $length): string
    {
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

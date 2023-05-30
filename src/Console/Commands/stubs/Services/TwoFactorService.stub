<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\ConFirmTwoFactorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorService
{

    /**
     * Handle the request.
     *
     * @param  \App\Http\Requests\ConFirmTwoFactorRequest  $request
     * @return ?array
     */
    public function handleConFirmTwoFactor(ConFirmTwoFactorRequest $request): ?array
    {
        $validated = $request->validated();

        if (!$request->user()->confirmTwoFactorAuth($validated['code']))
        {
            return null;
        }

        Auth::user()->update([
            'two_factor' => true
        ]);

        return $this->generateRecoveryCodes($request);
    }

    public function handleCreateTwoFactor(Request $request): ?array
    {
        if ($secret = $request->user()->createTwoFactorAuth()) {
            return [
                'qr_code' => $secret->toQr(),     // As QR Code
                'uri'     => $secret->toUri(),    // As "otpauth://" URI.
                'string'  => $secret->toString(), // As a string
            ];
        }

        return null;
    }

    public function handleDisableTwoFactor(Request $request): array
    {
        $request->user()->disableTwoFactorAuth();

        Auth::user()->update([
            'two_factor' => false
        ]);

        return [
            'message' => trans('sanctumauthstarter::auth.disabled_2fa'),
        ];
    }

    public function handleCurrentRecoveryCodes(Request $request): array
    {
        return [
            'message' => trans('sanctumauthstarter::auth.download_code'),
            'codes' => $request->user()->getRecoveryCodes()
        ];
    }

    public function handleNewRecoveryCodes(Request $request): array
    {
        return $this->generateRecoveryCodes($request);
    }

    public function isTwoFactorEnabled(): bool
    {
        return Auth::user()->two_factor;
    }

    private function generateRecoveryCodes(Request $request): array
    {
        return [
            'message' => trans('sanctumauthstarter::auth.download_code'),
            'codes' => $request->user()->generateRecoveryCodes()
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceRedirectController extends Controller
{
    /**
     * Redirect the user to different URLs based on their device (iOS/Android).
     */
    public function redirectToPlatform(Request $request)
    {
        $userAgent = $request->header('User-Agent', '');

        $androidUrl = 'https://play.google.com/store/apps/details?id=com.sanlymerkez.tanat&pcampaignid=web_share';
        $iosUrl = 'https://apps.apple.com/tm/app/tanat/id6504905083';

        if ($this->isIos($userAgent)) {
            return redirect()->away($iosUrl);
        } elseif ($this->isAndroid($userAgent)) {
            return redirect()->away($androidUrl);
        }

        return redirect()->away($androidUrl);
    }

    /**
     * Check if User-Agent belongs to an iOS device.
     */
    private function isIos(string $userAgent): bool
    {
        return (bool) preg_match('/iPhone|iPad|iPod/i', $userAgent);
    }

    /**
     * Check if User-Agent belongs to an Android device.
     */
    private function isAndroid(string $userAgent): bool
    {
        return (bool) preg_match('/Android/i', $userAgent);
    }
}

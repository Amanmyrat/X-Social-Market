<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class DeepLinkController extends Controller
{
    private string $androidUrl = 'https://play.google.com/store/apps/details?id=com.sanlymerkez.tanat&pcampaignid=web_share';
    private string $iosUrl = 'https://apps.apple.com/tm/app/tanat/id6504905083';
    private string $androidPackage = 'com.sanlymerkez.tanat';

    public function profile($profileId): Response
    {
        return $this->handleDeepLink('profile', $profileId);
    }

    public function post($postId): Response
    {
        return $this->handleDeepLink('post', $postId);
    }

    private function handleDeepLink($type, $id): Response
    {
        $userAgent = request()->header('User-Agent', '');
        $isIOS = $this->isIos($userAgent);
        $isAndroid = $this->isAndroid($userAgent);
        $isMobile = $isIOS || $isAndroid;

        // Build deep link URLs
        $customScheme = "tanat://{$type}/{$id}";
        $intentUrl = $isAndroid ? $this->buildAndroidIntent($type, $id) : null;
        $storeUrl = $isIOS ? $this->iosUrl : $this->androidUrl;

        $iosAppId = '6504905083';

        return response()->view('deeplink', compact(
            'customScheme',
            'intentUrl',
            'storeUrl',
            'isIOS',
            'isAndroid',
            'isMobile',
            'type',
            'id',
            'iosAppId',
        ));
    }

    private function buildAndroidIntent($type, $id): string
    {
        // Android Intent URL - most reliable for Android
        return "intent://{$type}/{$id}#Intent;" .
               "scheme=tanat;" .
               "package={$this->androidPackage};" .
               "S.browser_fallback_url=" . urlencode($this->androidUrl) . ";" .
               "end";
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

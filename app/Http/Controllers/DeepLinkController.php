<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeepLinkController extends Controller
{
    private $androidUrl = 'https://play.google.com/store/apps/details?id=com.sanlymerkez.tanat&pcampaignid=web_share';
    private $iosUrl = 'https://apps.apple.com/tm/app/tanat/id6504905083';

    public function profile($profileId)
    {
        $userAgent = request()->header('User-Agent', '');
        $deepLinkUrl = 'tanat://profile/' . $profileId;

        if ($this->isIos($userAgent)) {
            return redirect()->away($deepLinkUrl);
        } elseif ($this->isAndroid($userAgent)) {
            return redirect()->away($deepLinkUrl);
        }

        // If not mobile or deep link fails, redirect to appropriate store
        if ($this->isIos($userAgent)) {
            return redirect()->away($this->iosUrl);
        }
        
        return redirect()->away($this->androidUrl);
    }

    public function post($postId)
    {
        $userAgent = request()->header('User-Agent', '');
        $deepLinkUrl = 'tanat://post/' . $postId;

        if ($this->isIos($userAgent)) {
            return redirect()->away($deepLinkUrl);
        } elseif ($this->isAndroid($userAgent)) {
            return redirect()->away($deepLinkUrl);
        }

        // If not mobile or deep link fails, redirect to appropriate store
        if ($this->isIos($userAgent)) {
            return redirect()->away($this->iosUrl);
        }
        
        return redirect()->away($this->androidUrl);
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
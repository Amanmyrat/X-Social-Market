<?php

return [
    'ios' => [
        'latest_version' => env('IOS_LATEST_VERSION', '1.0.0'),
        'min_required_version' => env('IOS_MIN_REQUIRED_VERSION', '1.0.0'),
        'update_url' => env('IOS_UPDATE_URL', 'https://apps.apple.com/app/your-app-id')
    ],
    'android' => [
        'latest_version' => env('ANDROID_LATEST_VERSION', '1.0.0'),
        'min_required_version' => env('ANDROID_MIN_REQUIRED_VERSION', '1.0.0'),
        'update_url' => env('ANDROID_UPDATE_URL', 'https://play.google.com/store/apps/details?id=your.app.package')
    ]
]; 
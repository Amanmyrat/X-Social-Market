<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening Tanat...</title>
    
    @if($isIOS)
        <!-- iOS Universal Links meta tags -->
        <meta name="apple-itunes-app" content="app-id={{ $iosAppId }}">
    @endif
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            text-align: center;
            padding: 50px 20px;
            background: #f8f9fa;
            margin: 0;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .loading {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .fallback-btn {
            display: inline-block;
            background: #007AFF;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading" id="loading">Opening Tanat app...</div>
        <div class="hidden" id="fallback">
            <p>App didn't open automatically?</p>
            <a href="{{ $storeUrl }}" class="fallback-btn">
                @if($isIOS)
                    Open in App Store
                @else
                    Open in Google Play
                @endif
            </a>
        </div>
    </div>

    <script>
        (function() {
            let appOpened = false;
            let startTime = Date.now();
            
            // Method 1: Android Intent (most reliable for Android)
            @if($isAndroid && $intentUrl)
                function tryAndroidIntent() {
                    window.location.href = '{{ $intentUrl }}';
                }
            @endif

            // Method 2: Custom scheme (universal)
            function tryCustomScheme() {
                window.location.href = '{{ $customScheme }}';
            }

            // Method 3: Iframe attempt (some browsers)
            function tryIframe() {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = '{{ $customScheme }}';
                document.body.appendChild(iframe);
                setTimeout(() => document.body.removeChild(iframe), 1000);
            }

            // Detection methods
            function detectAppOpen() {
                // Method 1: Page visibility
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden && Date.now() - startTime < 3000) {
                        appOpened = true;
                    }
                });

                // Method 2: Window blur
                window.addEventListener('blur', function() {
                    if (Date.now() - startTime < 3000) {
                        appOpened = true;
                    }
                });

                // Method 3: Pagehide (mobile Safari)
                window.addEventListener('pagehide', function() {
                    appOpened = true;
                });
            }

            // Start detection
            detectAppOpen();

            // Try opening the app
            @if($isAndroid && $intentUrl)
                // Android: Use Intent URL first (most reliable)
                tryAndroidIntent();
                // Fallback to custom scheme after 500ms
                setTimeout(tryCustomScheme, 500);
            @else
                // iOS or other: Use custom scheme
                tryCustomScheme();
                // Also try iframe method
                setTimeout(tryIframe, 100);
            @endif

            // Fallback mechanism
            setTimeout(function() {
                if (!appOpened) {
                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('fallback').classList.remove('hidden');
                }
            }, 2500);

            // Ultimate fallback - redirect to store after 5 seconds
            setTimeout(function() {
                if (!appOpened) {
                    window.location.href = '{{ $storeUrl }}';
                }
            }, 5000);

        })();
    </script>

    <!-- Fallback for no-JS users -->
    <noscript>
        <meta http-equiv="refresh" content="0;url={{ $storeUrl }}">
    </noscript>
</body>
</html> 
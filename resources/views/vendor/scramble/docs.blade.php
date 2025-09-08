<!doctype html>
<html lang="en" data-theme="{{ config('scramble.theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} - API Docs</title>

    <script src="/vendor/scramble/web-components.min.js"></script>
    <link rel="stylesheet" href="/vendor/scramble/styles.min.css">
</head>
<body style="height: 100vh; overflow-y: hidden">
<elements-api
    apiDescriptionUrl="{{ route('scramble.docs.index') }}"
    tryItCredentialsPolicy="{{ config('scramble.ui.try_it_credentials_policy', 'include') }}"
    router="hash"
    @if(config('scramble.ui.hide_try_it')) hideTryIt="true" @endif
    logo="{{ config('scramble.ui.logo') }}"
/>
</body>
</html>

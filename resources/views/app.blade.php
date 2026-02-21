<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MasteYourCode</title>
    @vite(['resources/css/app.css', 'resources/js/main.js'])
    <script>
        window.AppConfig = {
            recaptchaSiteKey: "{{ config('recaptcha.site_key') }}"
        };
    </script>
</head>
<body class="h-full antialiased">
    <div id="app" class="h-full"></div>
</body>
</html>

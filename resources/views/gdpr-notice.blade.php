<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Informativa Privacy</title>
    @push("css")
        <link rel="stylesheet" href="/storage/framework/scss/compilati/app.css">
    @endPush
</head>
<body>
    <div class="container-fluid p-4">
        <h2 class="fw-bold text-lg mb-3">{{ __('fixcity::segnalazione.privacy.intro.text') }}</h2>
        <div class="mt-3">
            <p>{{ __('fixcity::segnalazione.privacy.details_prefix.text') }}</p>
            <p class="text-sm text-gray-600">{{ __('fixcity::segnalazione.privacy.privacy_label') }} <a href="/privacy" class="group text-blue-500 hover:underline"> {{ __('fixcity::segnalazione.privacy.link.label') }}</a></p>
        </div>
        <div class="mt-4">
            <p>{{ __('fixcity::segnalazione.privacy.text') }}</p>
        </div>
        <div class="mt-5 text-right">
            <a href="/segnalazioni" class="btn btn-primary btn-sm"> {{ __('fixcity::segnalazione.privacy.choose_link') }}</a>
        </div>
    </div>
</body>
</html>
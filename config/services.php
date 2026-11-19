<?php

declare(strict_types=1);

return [
    'facebook' => [
        'client_id' => config('env.FACEBOOK_CLIENT_ID'),
        'client_secret' => config('env.FACEBOOK_CLIENT_SECRET'),
        'redirect' => config('env.FACEBOOK_REDIRECT_URI'),
    ],
    'twitter' => [
        'client_id' => config('env.TWITTER_CLIENT_ID'),
        'client_secret' => config('env.TWITTER_CLIENT_SECRET'),
        'redirect' => config('env.TWITTER_REDIRECT_URI'),
    ],
    'instagram' => [
        'client_id' => config('env.INSTAGRAM_KEY'),
        'client_secret' => config('env.INSTAGRAM_SECRET'),
        'redirect' => config('env.INSTAGRAM_REDIRECT_URI'),
    ],
    'google' => [
        'client_id' => config('env.GOOGLE_CLIENT_ID'),
        'client_secret' => config('env.GOOGLE_CLIENT_SECRET'),
        'redirect' => config('env.GOOGLE_REDIRECT_URI'),
    ],
    'netfun' => [
        'token' => config('env.NETFUN_TOKEN'),
    ],
    'newsapi' => [
        'app_key' => config('env.NEWSAPI_APP_KEY'),
    ],
    'telegram-bot-api' => [
        'token' => (config('env.TELEGRAM_BOT_TOKEN') ?? 'YOUR BOT TOKEN HERE'),
        'bot_url' => config('env.TELEGRAM_BOT_URL'),
        'webhook' => config('env.TELEGRAM_BOT_WEBHOOK'),
    ],
];

<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Lingua in cui scrivere una notifica al destinatario (DCP-284): `users.lang` (impostata alla registrazione) se e' una lingua
 * supportata, altrimenti la lingua corrente. Serve perche' una notifica accodata esce nella lingua del worker, e un'azione
 * dell'operatore scatta nella lingua dell'operatore, non del cittadino: va passata a `Notification::locale()`.
 */
final class ResolveNotificationLocaleAction
{
    use QueueableAction;

    public function execute(UserContract $recipient): string
    {
        $lang = $recipient->getAttribute('lang');

        if (is_string($lang) && array_key_exists($lang, LaravelLocalization::getSupportedLocales())) {
            return $lang;
        }

        return app()->getLocale();
    }
}

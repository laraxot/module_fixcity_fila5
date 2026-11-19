<?php

declare(strict_types=1);

return [
    // Il banner e' sempre attivo (DCP-153): con soli cookie tecnici informa, con cookie non tecnici dichiarati in gdpr.config.cookie.extra chiede il consenso.
    'cookie_banner_on' => true,

    // Retention promised by the privacy notice (lang/it/policy.md, "Periodo di conservazione"), in days. Scheduled in routes/console.php.
    'retention' => [
        // "Registri degli accessi": 12 months. The operations log (activity_log) uses activitylog.clean_after_days.
        'authentication_log_days' => 365,
        // An account deleted by its owner is purged this many days after the request, and only once none of its reports is
        // still open (PurgeDeletedAccountsAction). The notice states no number of days: keep the two in step.
        'deleted_account_days' => 30,
    ],
];

<?php

declare(strict_types=1);

// DCP-286: Meldungen beim Absenden des Meldungsassistenten (Absenden läuft bereits, Sitzung abgelaufen). Gleiche Schlüssel in it, en, de, es.
return [
    'in_progress' => 'Wir senden diese Meldung noch. Warten Sie einige Sekunden und prüfen Sie Ihre Meldungen im persönlichen Bereich, bevor Sie es erneut versuchen.',
    'session_expired' => [
        'title' => 'Sitzung abgelaufen',
        'text' => 'Ihre Sitzung ist abgelaufen, daher wurde die Meldung nicht gesendet. Ihre Eingaben sind noch auf dieser Seite: Melden Sie sich in einem neuen Tab erneut an, kehren Sie dann hierher zurück und klicken Sie auf Senden.',
        'login_again' => 'Erneut anmelden',
        'new_tab' => 'öffnet sich in einem neuen Tab',
    ],
];

<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della sezione "I tuoi dati" di `/{lang}/area-personale/impostazioni` (DCP-111): in sola lettura, solo dell'utente indicato,
 * solo quello che il sistema conserva (nome, email, telefono dal profilo). Le righe senza valore non esistono; nessuna PII in piu'.
 * Il codice fiscale non ha una sorgente (stessa regola di BuildTicketReviewSummaryAction) e quindi non compare.
 */
final class BuildPersonalAreaSettingsDataAction
{
    use QueueableAction;

    /**
     * @return array{account_rows: list<array{label: string, value: string}>, email_verified: bool}
     */
    public function execute(UserContract $user): array
    {
        $name = trim(SafeStringCastAction::cast($user->name));
        if ($name === '') {
            $name = trim(SafeStringCastAction::cast($user->first_name).' '.SafeStringCastAction::cast($user->last_name));
        }

        $values = [
            'name' => $name,
            'email' => trim(SafeStringCastAction::cast($user->email)),
            'phone' => trim(SafeStringCastAction::cast(data_get($user->profile, 'phone'))),
        ];

        $rows = [];
        foreach ($values as $field => $value) {
            if ($value !== '') {
                $rows[] = ['label' => (string) __('fixcity::personal_area_settings.settings.account.fields.'.$field), 'value' => $value];
            }
        }

        return ['account_rows' => $rows, 'email_verified' => $user->hasVerifiedEmail()];
    }
}

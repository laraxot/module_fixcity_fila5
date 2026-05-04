<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Concerns;

use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Trait HasTicketAuthorData.
 *
 * Provides shared logic for retrieving and formatting authenticated user data
 * for ticket-related forms and schemas.
 */
trait HasTicketAuthorData
{
    protected static function getAuthUserName(): string
    {
        $user = auth()->user();
        if ($user === null) {
            return '';
        }

        return SafeStringCastAction::cast(data_get($user, 'name')
            ?? trim(SafeStringCastAction::cast(data_get($user, 'first_name', '')).' '.SafeStringCastAction::cast(data_get($user, 'last_name', ''))));
    }

    protected static function getAuthUserFiscalCode(): string
    {
        $user = auth()->user();
        if ($user === null) {
            return '';
        }

        return SafeStringCastAction::cast(data_get($user, 'fiscal_code')
            ?? data_get($user, 'codice_fiscale')
            ?? '');
    }

    protected static function getAuthUserPhone(): string
    {
        $user = auth()->user();
        if ($user === null) {
            return '';
        }

        return SafeStringCastAction::cast(data_get($user, 'phone')
            ?? data_get($user, 'mobile')
            ?? data_get($user, 'telefono')
            ?? '');
    }

    protected static function getAuthUserEmail(): string
    {
        $user = auth()->user();
        if ($user === null) {
            return '';
        }

        return SafeStringCastAction::cast(data_get($user, 'email') ?? '');
    }
}

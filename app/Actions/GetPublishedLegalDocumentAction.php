<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Tenant\Actions\Markdown\GetLocalizedMarkdownPathAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Testo Markdown di un documento legale del tenant attivo nella lingua corrente (`config/<tenant>/lang/<lingua>/<file>`, poi
 * `config/<tenant>/<file>`): informativa privacy (`policy.md`), termini/disclaimer (`terms.md`), note legali (`legal-notes.md`), media policy (`media-policy.md`).
 * Il testo lo scrive l'ente: senza file, file vuoto o ancora il modello iniziale ("Edit this file ...") restituisce null.
 * I segnaposto `{{ente.<campo>}}` (titolare, DPO, recapiti) sono gia' sostituiti con `config('ente')` da InterpolateEnteTokensAction, per
 * tutti i lettori (pagina, verifica delle sezioni, wizard della segnalazione): un dato mancante resta `[[ente.<campo>]]` e la verifica lo segnala.
 */
class GetPublishedLegalDocumentAction
{
    use QueueableAction;

    public function execute(string $file): ?string
    {
        $path = app(GetLocalizedMarkdownPathAction::class)->execute($file);
        if ($path === '#' || ! File::isFile($path)) {
            return null;
        }

        $content = trim(File::get($path));
        if ($content === '' || Str::contains($content, 'Edit this file')) {
            return null;
        }

        return app(InterpolateEnteTokensAction::class)->execute($content);
    }
}

<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Cms\Models\Page;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della dichiarazione di accessibilita' (DCP-132) come li ha scritti l'ente nel JSON della pagina `accessibilita.index`, nel blocco
 * della lingua corrente (chiave `declaration`). Il codice non contiene nessun valore: senza JSON o senza chiave restituisce un array vuoto.
 * Chi deve decidere dove spedire una segnalazione legge SEMPRE da qui (lato server) e mai da una proprieta' Livewire, che e' del client.
 */
class GetAccessibilityDeclarationAction
{
    use QueueableAction;

    public const string PAGE_SLUG = 'accessibilita.index';

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $declaration = data_get(Page::findUniqueBySlug(self::PAGE_SLUG)?->content_blocks, '0.data.declaration');
        if (! is_array($declaration)) {
            return [];
        }

        $normalized = [];
        foreach ($declaration as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }
}

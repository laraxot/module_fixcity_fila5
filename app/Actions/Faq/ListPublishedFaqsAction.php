<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Faq;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Database\Seeders\Support\CivicFaqCatalog;
use Modules\Fixcity\Models\Faq;
use Throwable;

/**
 * Elenco pubblico delle FAQ nella lingua della pagina.
 *
 * Se la tabella non c'è ancora, o è vuota, resta il catalogo civico:
 * la pagina non torna una griglia vuota.
 */
final class ListPublishedFaqsAction
{
    /**
     * @return list<array{question: string, answer: string}>
     */
    public function execute(?string $locale = null): array
    {
        $locale = $this->locale($locale);

        /** @var Collection<int, Faq> $rows */
        $rows = new Collection();

        try {
            if (! Schema::connection('fixcity')->hasTable('faqs')) {
                return CivicFaqCatalog::pairs($locale);
            }

            $rows = Faq::query()
                ->published()
                ->forLocale($locale)
                ->orderBy('sort_order')
                ->get();

            if ($rows->isEmpty() && $locale !== 'it') {
                $rows = Faq::query()
                    ->published()
                    ->forLocale('it')
                    ->orderBy('sort_order')
                    ->get();
            }
        } catch (Throwable) {
            return CivicFaqCatalog::pairs($locale);
        }

        if ($rows->isEmpty()) {
            return CivicFaqCatalog::pairs($locale);
        }

        $items = [];
        foreach ($rows as $row) {
            $question = trim((string) $row->getAttribute('question'));
            $answer = trim((string) $row->getAttribute('answer'));
            if ($question === '' || $answer === '') {
                continue;
            }
            $items[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $items !== [] ? $items : CivicFaqCatalog::pairs($locale);
    }

    private function locale(?string $locale): string
    {
        $value = $locale ?? app()->getLocale();

        return in_array($value, ['it', 'en', 'de', 'es'], true) ? $value : 'it';
    }
}

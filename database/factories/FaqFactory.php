<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use LogicException;
use Modules\Fixcity\Database\Seeders\Support\CivicFaqCatalog;
use Modules\Fixcity\Models\Faq;

/**
 * Una FAQ di test resta una domanda da cittadino, non un lorem.
 *
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entries = CivicFaqCatalog::forLocale('it');
        if ($entries === []) {
            throw new LogicException('The Italian FAQ catalog must not be empty.');
        }
        /** @var non-empty-list<array{slug: non-empty-string, sort_order: int, question: non-empty-string, answer: non-empty-string}> $entries */
        $entry = $entries[array_rand($entries)];

        return [
            'id' => 'it.'.Str::lower((string) Str::ulid()),
            'locale' => 'it',
            'question' => $entry['question'],
            'answer' => $entry['answer'],
            'sort_order' => $entry['sort_order'],
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state([
            'is_published' => false,
        ]);
    }
}

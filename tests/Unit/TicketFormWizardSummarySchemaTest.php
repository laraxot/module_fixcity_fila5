<?php

declare(strict_types=1);

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketFormReviewInfolist;
use Modules\Fixcity\Tests\TestCase;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * `getDefaultChildComponents()` e' dichiarato `array | Schema`: qui si normalizza ad array (si ispeziona lo schema, non si renderizza).
 *
 * @return array<array-key, SchemaComponent|Action|ActionGroup|string|Htmlable>
 */
function summaryChildren(SchemaComponent $component): array
{
    $children = $component->getDefaultChildComponents();

    return $children instanceof Schema ? $children->getComponents() : $children;
}

/**
 * @param  array<array-key, SchemaComponent|Action|ActionGroup|string|Htmlable>  $components
 * @param  class-string<SchemaComponent>  $class
 */
function summaryContains(array $components, string $class): bool
{
    foreach ($components as $component) {
        if ($component instanceof $class) {
            return true;
        }

        if ($component instanceof Section && summaryContains(summaryChildren($component), $class)) {
            return true;
        }
    }

    return false;
}

describe('ticket wizard summary step schema', function (): void {
    it('uses filament infolist text entries for the data recap block', function (): void {
        $entries = TicketFormReviewInfolist::summarySectionEntries();

        foreach (
            [
                'review_location',
                'review_type',
                'review_priority',
                'review_name',
                'review_content',
                'review_images',
            ] as $key
        ) {
            Assert::assertArrayHasKey($key, $entries);
            Assert::assertInstanceOf(TextEntry::class, $entries[$key]);
        }
    });

    it('does not collect personal details that the ticket does not persist', function (): void {
        $defaults = TicketForm::getDefaultFormState();
        foreach (['author_name', 'author_fiscal_code', 'author_phone', 'author_email'] as $field) {
            Assert::assertArrayNotHasKey($field, $defaults);
        }
    });

    it('provides localized labels for the summary entries in Italian and English', function (): void {
        foreach ([
            'it' => ['Posizione', 'Categoria', 'Priorità', 'Titolo', 'Descrizione', 'Allegati'],
            'en' => ['Location', 'Category', 'Priority', 'Title', 'Description', 'Attachments'],
        ] as $locale => $labels) {
            app('translator')->setLocale($locale);

            foreach ([
                'review_location',
                'review_type',
                'review_priority',
                'review_name',
                'review_content',
                'review_images',
            ] as $index => $field) {
                Assert::assertSame(
                    $labels[$index],
                    __("fixcity::ticket_form_review_infolist.fields.{$field}.label"),
                );
            }
        }

        app('translator')->setLocale('it');
        Assert::assertSame('', __('fixcity::ticket_form.sections.empty.heading'));
    });

    it('keeps priority as an internal default instead of a visible data-step select', function (): void {
        $schema = TicketForm::getDataSchema();

        Assert::assertInstanceOf(Hidden::class, $schema['priority'] ?? null);
        Assert::assertInstanceOf(Section::class, $schema['locationSection'] ?? null);
        Assert::assertInstanceOf(Section::class, $schema['issueSection'] ?? null);

        Assert::assertTrue(summaryContains(summaryChildren($schema['issueSection']), Select::class));
    });

    it('keeps the location map visible and read-only in the summary step', function (): void {
        $summary = TicketForm::getSummarySchema()['summarySection'] ?? null;

        Assert::assertInstanceOf(Section::class, $summary);
        $maps = array_filter(summaryChildren($summary), static fn (object|string $child): bool => $child instanceof CoordinatePicker);

        Assert::assertCount(1, $maps);
        Assert::assertTrue($maps[array_key_first($maps)]->isDisabled());
    });
});

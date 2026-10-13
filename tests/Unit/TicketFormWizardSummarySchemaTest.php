<?php

declare(strict_types=1);

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Section;
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketFormReviewInfolist;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('ticket wizard summary step schema', function (): void {
    /** @var Closure(array<int|string, SchemaComponent>, string): bool $containsComponent */
    $containsComponent = function (array $components, string $class) use (&$containsComponent): bool {
        foreach ($components as $component) {
            if ($component instanceof $class) {
                return true;
            }

            if ($component instanceof Section && $containsComponent($component->getChildComponents(), $class)) {
                return true;
            }
        }

        return false;
    };

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

    it('keeps priority as an internal default instead of a visible data-step select', function () use ($containsComponent): void {
        $schema = TicketForm::getDataSchema();

        Assert::assertInstanceOf(Hidden::class, $schema['priority'] ?? null);
        Assert::assertNotInstanceOf(Select::class, $schema['priority']);
        Assert::assertInstanceOf(Section::class, $schema['locationSection'] ?? null);
        Assert::assertInstanceOf(Section::class, $schema['issueSection'] ?? null);

        $issueComponents = $schema['issueSection']->getDefaultChildComponents();
        Assert::assertTrue($containsComponent($issueComponents, Select::class));
    });

    it('keeps the location map visible and read-only in the summary step', function (): void {
        $summary = TicketForm::getSummarySchema()['summarySection'] ?? null;

        Assert::assertInstanceOf(Section::class, $summary);
        $components = $summary->getDefaultChildComponents();
        $maps = array_filter($components, static fn (SchemaComponent $component): bool => $component instanceof CoordinatePicker);

        Assert::assertCount(1, $maps);
        Assert::assertTrue($maps[array_key_first($maps)]->isDisabled());
    });
});

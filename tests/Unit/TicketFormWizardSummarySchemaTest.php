<?php

declare(strict_types=1);

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketFormReviewInfolist;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

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
        Assert::assertNotInstanceOf(Select::class, $schema['priority']);
        Assert::assertInstanceOf(Select::class, $schema['type'] ?? null);
    });
});

<?php

declare(strict_types=1);

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketFormReviewInfolist;
use PHPUnit\Framework\Assert;
use ReflectionMethod;

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

    it('uses form text inputs for author and contacts on the same step', function (): void {
        $authMethod = new ReflectionMethod(TicketForm::class, 'getAuthorSectionSchema');
        /** @var array<string, mixed> $author */
        $author = $authMethod->invoke(null);

        $contactsMethod = new ReflectionMethod(TicketForm::class, 'getContactsSectionSchema');
        /** @var array<string, mixed> $contacts */
        $contacts = $contactsMethod->invoke(null);

        Assert::assertInstanceOf(TextInput::class, $author['authorFiscalCode'] ?? null);
        Assert::assertInstanceOf(TextInput::class, $author['authorName'] ?? null);
        Assert::assertInstanceOf(TextInput::class, $contacts['authorPhone'] ?? null);
        Assert::assertInstanceOf(TextInput::class, $contacts['authorEmail'] ?? null);
    });

    it('keeps priority as an internal default instead of a visible data-step select', function (): void {
        $schema = TicketForm::getDataSchema();

        Assert::assertInstanceOf(Hidden::class, $schema['priority'] ?? null);
        Assert::assertNotInstanceOf(Select::class, $schema['priority'] ?? null);
        Assert::assertInstanceOf(Select::class, $schema['type'] ?? null);
    });
});

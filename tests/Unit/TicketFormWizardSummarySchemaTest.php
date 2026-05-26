<?php

declare(strict_types=1);

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketFormReviewInfolist;

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
            expect($entries)->toHaveKey($key)
                ->and($entries[$key])->toBeInstanceOf(TextEntry::class);
        }
    });

    it('uses form text inputs for author and contacts on the same step', function (): void {
        $authMethod = new ReflectionMethod(TicketForm::class, 'getAuthorSectionSchema');
        /** @var array<string, mixed> $author */
        $author = $authMethod->invoke(null);

        $contactsMethod = new ReflectionMethod(TicketForm::class, 'getContactsSectionSchema');
        /** @var array<string, mixed> $contacts */
        $contacts = $contactsMethod->invoke(null);

        expect($author['authorName'] ?? null)->toBeInstanceOf(TextInput::class)
            ->and($author['authorFiscalCode'] ?? null)->toBeInstanceOf(TextInput::class)
            ->and($contacts['authorPhone'] ?? null)->toBeInstanceOf(TextInput::class)
            ->and($contacts['authorEmail'] ?? null)->toBeInstanceOf(TextInput::class);
    });

    it('keeps priority as an internal default instead of a visible data-step select', function (): void {
        $schema = TicketForm::getDataSchema();

        expect($schema['type'] ?? null)->toBeInstanceOf(Select::class)
            ->and($schema['priority'] ?? null)->toBeInstanceOf(Hidden::class)
            ->and($schema['priority'] ?? null)->not->toBeInstanceOf(Select::class);
    });
});

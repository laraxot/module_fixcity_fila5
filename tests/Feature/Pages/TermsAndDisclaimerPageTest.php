<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

it('publishes the localized terms page through the shared legal document renderer', function (string $locale, string $title): void {
    app()->instance(GetPublishedLegalDocumentAction::class, new class extends GetPublishedLegalDocumentAction
    {
        public function execute(string $file): ?string
        {
            return $file === 'terms.md' ? "# Terms supplied by the owner\n\nApproved service terms." : null;
        }
    });

    $this->get("/{$locale}/terms")
        ->assertOk()
        ->assertSee($title)
        ->assertSee('Approved service terms.')
        ->assertDontSee('data-element="legal-incomplete"', false);
})->with([
    ['it', 'Termini e disclaimer'],
    ['en', 'Terms and disclaimer'],
    ['de', 'Nutzungsbedingungen und Haftungshinweis'],
    ['es', 'Términos y aviso legal'],
]);

it('keeps the terms page visibly incomplete when the tenant has no approved text', function (): void {
    app()->instance(GetPublishedLegalDocumentAction::class, new class extends GetPublishedLegalDocumentAction
    {
        public function execute(string $file): ?string
        {
            return null;
        }
    });

    $this->get('/it/terms')
        ->assertOk()
        ->assertSee('data-element="legal-incomplete"', false)
        ->assertDontSee('data-element="legal-text"', false);
});

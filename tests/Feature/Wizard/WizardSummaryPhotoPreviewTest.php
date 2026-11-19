<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;

uses(TestCase::class);

/*
 * DCP-182, stadio 3 del piano (`docs/blueprints/riepilogo-anteprima-foto.md`): le foto caricate nel passo dei dati arrivano al passo riepilogo come
 * `TemporaryUploadedFile` e il riepilogo mostra l'insieme ATTUALE (non quello del primo ingresso): togliendone una e aggiungendone un'altra le anteprime
 * seguono. Percorso reale: upload Livewire -> stato `data.images` -> `BuildTicketReviewSummaryAction` -> vista del tema.
 */
beforeEach(function (): void {
    Storage::fake('public');
    app()->setLocale('it');
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });
    Auth::login(UserFactory::new()->createOne());
});

/** @return list<string> URL delle miniature (attributo `src` degli `<img>` del gruppo anteprime) */
function previewSources(string $html): array
{
    $start = strpos($html, 'data-element="review-attachments"');
    if ($start === false) {
        return [];
    }
    $group = substr($html, $start, 4000);
    preg_match_all('/<img [^>]*src="([^"]+)"/', $group, $m);

    return array_map(html_entity_decode(...), $m[1]);
}

test('shows a preview for each uploaded photo and follows the removal and the new upload', function (): void {
    $component = Livewire::test(CreateTicketWizardWidget::class)
        ->set('data.privacyAccepted', true)
        ->upload('data.images', [UploadedFile::fake()->image('prima.jpg', 40, 30), UploadedFile::fake()->image('seconda.png', 40, 30)], true);

    $two = previewSources($component->html());
    Assert::assertCount(2, $two, 'due foto caricate, due miniature');
    foreach ($two as $src) {
        Assert::assertStringContainsString('/preview-file/', $src);
    }
    $component->assertSee('prima.jpg')->assertSee('seconda.png');

    // si toglie una foto e se ne aggiunge una in formato non anteprimabile (PDF): resta una sola miniatura, il PDF solo nel nome
    $images = (array) $component->get('data.images');
    Assert::assertCount(2, $images);
    $component->set('data.images', array_slice($images, 1, 1, true))
        ->upload('data.images', [UploadedFile::fake()->create('modulo.pdf', 20, 'application/pdf')], true);

    $one = previewSources($component->html());
    Assert::assertCount(1, $one, 'una miniatura (png), il pdf non ne ha');
    $component->assertSee('seconda.png')->assertSee('modulo.pdf')->assertDontSee('prima.jpg');
});

test('adds no preview group when nothing is uploaded', function (): void {
    $html = Livewire::test(CreateTicketWizardWidget::class)->set('data.privacyAccepted', true)->html();

    Assert::assertStringNotContainsString('data-element="review-attachments"', $html);
});

test('refuses a livewire upload aimed at something that is not an upload field of the widget (audit A-2)', function (): void {
    $component = Livewire::test(CreateTicketWizardWidget::class)->set('data.privacyAccepted', true);

    $thrown = null;
    try {
        $component->upload('data.name', [UploadedFile::fake()->image('x.png')], true);
    } catch (Throwable $e) {
        $thrown = $e;
    }

    Assert::assertNotNull($thrown, 'l\'upload verso un campo che non e\' di caricamento deve essere rifiutato');
});

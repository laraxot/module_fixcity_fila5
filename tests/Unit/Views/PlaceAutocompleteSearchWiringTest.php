<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

/*
 * DCP-286 (FO-3-07): "Cerca un luogo" era un campo di testo legato solo a `data.location.address`: scrivere un indirizzo e premere Invio non cercava
 * nulla (misurato su :8091: nessuna richiesta a Nominatim, latitudine e longitudine vuote, "indirizzo obbligatorio" ancora visibile). Ora il campo
 * usa la ricerca che la mappa ha gia' (il pannello della lente, `coordinate-picker-lit`) e dice cosa succede. Il fornitore del geocoder non cambia (E-05).
 * Contratto del markup: il comportamento nel browser e' provato dalla sonda Playwright della story.
 */

it('wires the place field to the map search on Enter and on leaving the field', function (): void {
    $html = Livewire::test(CreateTicketWizardWidget::class)->html();

    expect($html)
        ->toContain('x-ref="field"')
        ->toContain('x-on:keydown.enter.prevent="search(true)"')
        ->toContain('x-on:change="search(false)"')
        ->toContain('address-selected')
        ->toContain('.search-box');
});

it('carries the search messages of the current language', function (string $locale, string $searching, string $notFound): void {
    app()->setLocale($locale);
    $html = Livewire::test(CreateTicketWizardWidget::class)->html();

    expect(html_entity_decode($html, ENT_QUOTES))
        ->toContain($searching)
        ->toContain($notFound);
})->with([
    'italiano' => ['it', 'Cerco il luogo...', 'Non trovo questo luogo.'],
    'inglese' => ['en', 'Searching for the place...', 'We could not find this place.'],
    'tedesco' => ['de', 'Ort wird gesucht ...', 'Wir finden diesen Ort nicht.'],
    'spagnolo' => ['es', 'Buscando el lugar...', 'No encontramos este lugar.'],
]);

/*
 * Errore vecchio che resta (FO-3-07, FO-3-17): "Il campo indirizzo del disservizio e' obbligatorio." restava visibile a campo compilato fino al
 * successivo "Avanti" (misurato su :8091), perche' il campo non rivalidava quando cambiava. Indirizzo e punto sulla mappa rivalidano da soli.
 */
it('clears the stale address and point errors as soon as they are filled', function (): void {
    /** @var TestCase $this */
    $this->actingAs(UserFactory::new()->createOne());
    app()->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });

    $component = Livewire::test(CreateTicketWizardWidget::class);
    $component->set('data.location', ['latitude' => null, 'longitude' => null, 'address' => null])->call('submit');
    $component->assertHasErrors(['data.location.address', 'data.location']);

    $component->set('data.location.address', 'Via Solferino 10, Firenze')->assertHasNoErrors(['data.location.address']);
    $component->set('data.location', ['latitude' => 43.7756, 'longitude' => 11.2396, 'address' => 'Via Solferino 10, Firenze'])
        ->assertHasNoErrors(['data.location']);
});

/*
 * Passo 2 con campi mancanti (FO-3-17, FO-3-23): "Avanti" lasciava il focus sul pulsante, e chi usa tastiera o screen reader non sapeva dove
 * correggere. Dopo un "Avanti" respinto il focus va al primo campo non valido (che ha `aria-invalid` e `aria-describedby` verso il suo errore).
 */
it('moves the focus to the first invalid field after a refused Next, from step 2 on', function (): void {
    $html = Livewire::test(CreateTicketWizardWidget::class)->html();

    // il primo passo ha la sua gestione del consenso (focusPrivacyError): "Avanti" li' non cambia
    expect($html)
        ->toContain('x-on:click="requestNextStep()"')
        ->and(substr_count($html, 'await requestNextStep(); await $nextTick();'))->toBe(1)
        ->and($html)->toContain('[aria-invalid=true]')
        ->and($html)->toContain('.focus()');
});

/*
 * Il messaggio sul punto mancante era un testo da sviluppatore ("latitudine tra -90 e 90, longitudine tra -180 e 180") mostrato accanto a
 * "indirizzo obbligatorio": ora dice al cittadino cosa fare.
 */
it('tells the citizen what to do when the point is missing, without latitude and longitude jargon', function (string $locale, string $expected, string $jargon): void {
    app()->setLocale($locale);

    $message = (string) __('fixcity::ticket_form.fields.location.invalid');

    expect($message)->toBe($expected)->not->toContain($jargon)->not->toContain('-180');
})->with([
    'italiano' => ['it', 'Scegli il punto sulla mappa o cerca un indirizzo: serve per sapere dove si trova il disservizio.', 'latitudine'],
    'inglese' => ['en', 'Choose the point on the map or search for an address: we need it to know where the problem is.', 'latitude'],
    'tedesco' => ['de', 'Wählen Sie den Punkt auf der Karte oder suchen Sie eine Adresse: Wir brauchen ihn, um zu wissen, wo die Störung ist.', 'Breitengrad'],
    'spagnolo' => ['es', 'Elige el punto en el mapa o busca una dirección: lo necesitamos para saber dónde está la incidencia.', 'latitud'],
]);

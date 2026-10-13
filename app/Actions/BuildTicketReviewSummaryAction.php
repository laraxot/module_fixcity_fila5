<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati del passo "riepilogo" del wizard `/tickets/create` (DCP-33), nella forma che la vista del tema
 * `pub_theme::components.wizard.summary-step` mostra come `segnalazione-03-riepilogo`.
 *
 * Regole: le righe esistono solo se il dato esiste (mai inventare); autore e contatti sono solo quelli
 * dell'utente autenticato (l'ospite non ha ne' l'uno ne' gli altri); il codice fiscale non ha una sorgente
 * (ne' colonna ne' campo) e quindi non compare mai; la priorita' non fa parte del riferimento.
 */
final class BuildTicketReviewSummaryAction
{
    use QueueableAction;

    /**
     * @param  array<string, mixed>  $state  Stato del form (`TicketForm::getDefaultFormState()`: name, type, content, location, images).
     * @param  Authenticatable|null  $user  Utente autenticato (`Filament::auth()->user()`); solo un {@see UserContract} conta.
     * @return array{
     *     report: array{
     *         title: string,
     *         card_title: string,
     *         edit_label: string,
     *         edit_step: string,
     *         rows: list<array{label: string, value: string}>,
     *         images: list<string>,
     *         location: array{latitude: float, longitude: float, address: string}|null,
     *         map_label: string
     *     },
     *     author: array{group_title: string, card_title: string, name: string, rows: list<array{label: string, value: string}>}|null,
     *     contacts: array{title: string, rows: list<array{label: string, value: string}>}|null
     * }
     */
    public function execute(array $state, ?Authenticatable $user = null): array
    {
        $user = $user instanceof UserContract ? $user : null;

        return [
            'report' => $this->report($state),
            'author' => $this->author($user),
            'contacts' => $this->contacts($user),
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array{
     *     title: string,
     *     card_title: string,
     *     edit_label: string,
     *     edit_step: string,
     *     rows: list<array{label: string, value: string}>,
     *     images: list<string>,
     *     location: array{latitude: float, longitude: float, address: string}|null,
     *     map_label: string
     * }
     */
    private function report(array $state): array
    {
        $location = $this->location($state['location'] ?? null);
        $images = $this->imageNames($state['images'] ?? null);
        $address = trim(SafeStringCastAction::cast(data_get($state, 'location.address')));

        return [
            'title' => (string) __('fixcity::ticket_review.report.title'),
            'card_title' => (string) __('fixcity::ticket_review.report.card_title'),
            'edit_label' => (string) __('fixcity::ticket_review.report.edit_label'),
            'edit_step' => $this->dataStepKey(),
            'rows' => $this->rows('report', [
                'address' => $address,
                'type' => $this->typeLabel($state['type'] ?? null),
                'name' => trim(SafeStringCastAction::cast($state['name'] ?? null)),
                'content' => trim(SafeStringCastAction::cast($state['content'] ?? null)),
                'images' => implode(', ', $images),
            ]),
            'images' => $images,
            'location' => $location,
            'map_label' => (string) __('fixcity::ticket_review.report.map_label'),
        ];
    }

    /**
     * @return array{group_title: string, card_title: string, name: string, rows: list<array{label: string, value: string}>}|null
     */
    private function author(?UserContract $user): ?array
    {
        if ($user === null) {
            return null;
        }

        $name = trim(SafeStringCastAction::cast($user->name));
        if ($name === '') {
            $name = trim(SafeStringCastAction::cast($user->first_name).' '.SafeStringCastAction::cast($user->last_name));
        }
        if ($name === '') {
            $name = trim(SafeStringCastAction::cast($user->email));
        }

        return [
            'group_title' => (string) __('fixcity::ticket_review.author.group_title'),
            'card_title' => (string) __('fixcity::ticket_review.author.card_title'),
            'name' => $name,
            // ponytail: il codice fiscale non ha una sorgente (nessuna colonna in users/profiles/tickets); aggiungerlo qui quando ne esistera' una.
            'rows' => [],
        ];
    }

    /**
     * @return array{title: string, rows: list<array{label: string, value: string}>}|null
     */
    private function contacts(?UserContract $user): ?array
    {
        if ($user === null) {
            return null;
        }

        $rows = $this->rows('contacts', [
            'phone' => trim(SafeStringCastAction::cast(data_get($user->profile, 'phone'))),
            'email' => trim(SafeStringCastAction::cast($user->email)),
        ]);

        return $rows === [] ? null : ['title' => (string) __('fixcity::ticket_review.contacts.title'), 'rows' => $rows];
    }

    /**
     * Righe `etichetta/valore` per i soli valori non vuoti; l'etichetta e' `fixcity::ticket_review.<gruppo>.fields.<chiave>`.
     *
     * @param  array<string, string>  $values
     * @return list<array{label: string, value: string}>
     */
    private function rows(string $group, array $values): array
    {
        $rows = [];
        foreach ($values as $field => $value) {
            if ($value !== '') {
                $rows[] = ['label' => (string) __('fixcity::ticket_review.'.$group.'.fields.'.$field), 'value' => $value];
            }
        }

        return $rows;
    }

    private function typeLabel(mixed $raw): string
    {
        $type = is_string($raw) ? TicketTypeEnum::tryFrom($raw) : $raw;

        return $type instanceof TicketTypeEnum ? $type->getLabel() : '';
    }

    /**
     * @return array{latitude: float, longitude: float, address: string}|null null senza coordinate valide
     */
    private function location(mixed $raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        $latitude = $this->coordinate($raw['latitude'] ?? null, 90.0);
        $longitude = $this->coordinate($raw['longitude'] ?? null, 180.0);
        if ($latitude === null || $longitude === null) {
            return null;
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => trim(SafeStringCastAction::cast($raw['address'] ?? null)),
        ];
    }

    private function coordinate(mixed $raw, float $limit): ?float
    {
        if (! is_int($raw) && ! is_float($raw) && ! (is_string($raw) && is_numeric($raw))) {
            return null;
        }

        $value = (float) $raw;

        return abs($value) <= $limit ? $value : null;
    }

    /**
     * Nomi dei file caricati (anche i `TemporaryUploadedFile` di Livewire sono {@see UploadedFile}); le stringhe sono
     * identificativi di media gia' salvati, non nomi da mostrare.
     *
     * @return list<string>
     */
    private function imageNames(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $names = [];
        foreach ($raw as $file) {
            $name = $file instanceof UploadedFile ? trim($file->getClientOriginalName()) : '';
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return $names;
    }

    /**
     * Chiave Livewire del passo dei dati, come la genera Filament (`Step::make()` senza chiave) e come la usa `?step=`:
     * dipende dalla lingua perche' nasce dall'etichetta del passo.
     */
    private function dataStepKey(): string
    {
        $label = (string) __('fixcity::fixcity.ticket.steps.data.label');

        return 'form.'.Str::slug(Str::transliterate($label, strict: true)).'::data::wizard-step';
    }
}

<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Scrittura sul database del dataset investitori: ticket, timeline di stato con SLA,
 * valutazione del cittadino e allegati fotografici.
 *
 * Separata da TicketSeeder, che compone i piani: qui vivono le scritture e le
 * attenzioni legate allo schema reale (colonne facoltative, campi non fillable,
 * mime type accettati dalle collezioni media).
 *
 * @phpstan-type CategoryRow array{
 *     id: non-empty-string,
 *     name: non-empty-string,
 *     description: non-empty-string,
 *     icon: non-empty-string,
 *     parent_id: non-empty-string|null,
 *     sort_order: int,
 *     type: TicketTypeEnum,
 *     weight: int,
 *     department: non-empty-string,
 *     titles: non-empty-list<non-empty-string>,
 *     details: non-empty-list<non-empty-string>
 * }
 * @phpstan-type MunicipalityRow array{
 *     name: non-empty-string,
 *     region: non-empty-string,
 *     province: non-empty-string,
 *     province_code: non-empty-string,
 *     postal_code: non-empty-string,
 *     lat: float,
 *     lng: float,
 *     population: int
 * }
 * @phpstan-type AddressRow array{address: non-empty-string, street: non-empty-string, street_number: non-empty-string, postal_code: non-empty-string}
 * @phpstan-type PersonRow array{id: int|string, email: non-empty-string, department: non-empty-string|null}
 * @phpstan-type Milestone array{status: TicketStatusEnum, at: Carbon, payload: array<string, mixed>}
 * @phpstan-type TicketPlan array{
 *     code: non-empty-string,
 *     slug: non-empty-string,
 *     title: non-empty-string,
 *     content: non-empty-string,
 *     status: TicketStatusEnum,
 *     priority: TicketPriorityEnum,
 *     type: TicketTypeEnum,
 *     category: CategoryRow,
 *     municipality: MunicipalityRow,
 *     address: AddressRow,
 *     coordinates: array{lat: float, lng: float},
 *     owner: PersonRow,
 *     assignee: PersonRow,
 *     responsible_id: int|string|null,
 *     created_at: Carbon,
 *     updated_at: Carbon,
 *     estimation: float,
 *     milestones: non-empty-list<Milestone>
 * }
 */
final class DemoTicketWriter
{
    /**
     * Percentuale di ticket che ricevono una foto allegata. Faker::boolean()
     * ragiona in percenti (0-100), non in probabilita' 0-1.
     */
    private const float PHOTO_RATE_PERCENT = 12.0;

    /**
     * Immagini usate come foto della segnalazione. Sono verificate a runtime con
     * `File::mimeType()`: alcuni screenshot del tema risultano corrotti (magic bytes
     * non riconosciuti) e le collezioni del Ticket accettano solo JPEG e PNG.
     *
     * @var list<non-empty-string>
     */
    private const array PHOTO_PLACEHOLDERS = [
        'Themes/Sixteen/docs/visual-comparison/screenshots/segnalazione-disservizio/reference-mobile.png',
        'Themes/Sixteen/docs/screenshots/2026-04-02/ref-rating.png',
        'Themes/Sixteen/docs/screenshots/fixcity-header-slim.png',
        'Themes/Sixteen/docs/screenshots/faq/04-local-hero.png',
    ];

    /**
     * @var list<non-empty-string>
     */
    private const array ACCEPTED_PHOTO_MIMES = ['image/png', 'image/jpeg'];

    private static ?bool $hasCitizenRating = null;

    public function __construct(private readonly string $codePrefix) {}

    /**
     * I soli ticket generati da questo dataset: il prefisso e' nel `code`, unico
     * campo realmente fillable, per non sfiorare DEMO-* e INV-*.
     *
     * @return Builder<Ticket>
     */
    public function demoTickets(): Builder
    {
        return Ticket::query()->where('code', 'like', $this->codePrefix.'-%');
    }

    /**
     * Rimuove la timeline dei soli ticket del dataset: le attivita' degli altri
     * seeder (DEMO-*, INV-*) non le tocca.
     */
    public function clearDemoActivities(): void
    {
        if (! self::hasActivityTable()) {
            return;
        }

        $ticketIds = $this->demoTickets()->pluck('id')->all();
        if ($ticketIds !== []) {
            TicketActivity::query()->whereIn('ticket_id', $ticketIds)->forceDelete();
        }
    }

    /**
     * Scrive il ticket, i timestamp del modello e la valutazione del cittadino.
     *
     * @param  TicketPlan  $plan
     */
    public function persist(Generator $faker, array $plan): int|string
    {
        $code = SafeStringCastAction::cast($plan['code']);
        $slug = SafeStringCastAction::cast($plan['slug']);

        /** @var Ticket $ticket */
        $ticket = Ticket::query()->updateOrCreate(['code' => $code], [
            'name' => $plan['title'],
            'content' => $plan['content'],
            'email' => $plan['owner']['email'],
            'owner_id' => $plan['owner']['id'],
            'responsible_id' => $plan['responsible_id'],
            'status' => $plan['status'],
            'priority' => $plan['priority'],
            'type' => $plan['type'],
            'estimation' => $plan['estimation'],
            'location' => self::locationPayload($plan),
            'created_by' => $plan['owner']['id'],
            'updated_by' => $plan['responsible_id'] ?? $plan['owner']['id'],
        ]);

        $attributes = [
            'created_at' => $plan['created_at'],
            'updated_at' => $plan['updated_at'],
            // `ticket_prefix` non e' fra i campi fillable di Ticket: va forzato,
            // altrimenti il prefisso verrebbe scartato in silenzio.
            'ticket_prefix' => $this->codePrefix,
        ];

        if (self::hasCitizenRatingColumns()) {
            [$rating, $ratedAt] = $this->citizenRating($faker, $plan);
            $attributes['citizen_rating'] = $rating;
            $attributes['citizen_rated_at'] = $ratedAt;
        }

        $ticket->forceFill($attributes)->save();

        // HasSlug rigenera lo slug da `name` a ogni salvataggio: lo ripristiniamo
        // perche' l'idempotenza del dataset non dipenda dal titolo scelto.
        Ticket::withoutEvents(static function () use ($ticket, $slug): void {
            Ticket::query()->whereKey($ticket->getKey())->update(['slug' => $slug]);
        });

        return self::keyOf($ticket);
    }

    /**
     * Chiave primaria del ticket: intero o uuid, secondo lo schema del database.
     */
    private static function keyOf(Model $model): int|string
    {
        $key = $model->getKey();

        return is_int($key) || is_string($key) ? $key : SafeStringCastAction::cast($key);
    }

    /**
     * Timeline di stato e SLA. Riparte da zero perche' la chiave di matching
     * (`reason`) e' un testo umano e non puo' garantire l'idempotenza.
     *
     * @param  TicketPlan  $plan
     */
    public function writeActivities(Generator $faker, array $plan, int|string $ticketId): void
    {
        if (! self::hasActivityTable()) {
            return;
        }

        foreach ($plan['milestones'] as $position => $milestone) {
            $from = $position === 0 ? null : $plan['milestones'][$position - 1]['status'];

            $this->recordActivity([
                'ticket_id' => $ticketId,
                'user_id' => $plan['responsible_id'] ?? $plan['owner']['id'],
                'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'reason' => $from === null
                    ? 'Segnalazione ricevuta dal cittadino e acquisita al protocollo.'
                    : DemoNarrative::activityReason($faker, $milestone['status']),
                'payload' => [
                    'source' => 'fixcity-demo',
                    'category' => $plan['category']['id'],
                    'department' => $plan['category']['department'],
                    'from' => $from?->value,
                    'to' => $milestone['status']->value,
                    'sla' => $milestone['payload'],
                ],
                'created_at' => $milestone['at'],
            ]);
        }

        $this->writeAssignment($plan, $ticketId);
    }

    /**
     * Assegnazione all'ufficio competente: evento separato dallo stato, cosi'
     * l'elenco attivita' mostra chi ha preso in carico cosa e quando. L'assegnazione
     * coincide con il sopralluogo, non con la chiusura.
     *
     * @param  TicketPlan  $plan
     */
    private function writeAssignment(array $plan, int|string $ticketId): void
    {
        if ($plan['responsible_id'] === null) {
            return;
        }

        $this->recordActivity([
            'ticket_id' => $ticketId,
            'user_id' => $plan['responsible_id'],
            'event_type' => TicketActivityEventTypeEnum::Assignment->value,
            'visibility' => TicketActivityVisibilityEnum::Public->value,
            'reason' => 'Ticket assegnato a: '.$plan['category']['department'].'.',
            'payload' => [
                'source' => 'fixcity-demo',
                'department' => $plan['category']['department'],
                'assignee_id' => $plan['assignee']['id'],
                'assignee_email' => $plan['assignee']['email'],
            ],
            'created_at' => self::assignmentMoment($plan),
        ]);
    }

    /**
     * @param  array<string, mixed>  $activity
     */
    private function recordActivity(array $activity): void
    {
        $at = $activity['created_at'];
        unset($activity['created_at']);

        $record = TicketActivity::query()->create($activity);
        $record->forceFill(['created_at' => $at, 'updated_at' => $at])->save();
    }

    /**
     * Allega una foto di segnalazione. Senza file utilizzabile, o senza il supporto
     * media, la demo resta comunque leggibile: il fallimento non e' fatale.
     *
     * La scelta della foto e' ancorata al `code` del ticket, non al generatore
     * casuale: rilanciando il seeder gli stessi ticket restano fotografati e gli
     * altri restano senza foto.
     */
    public function maybeAttachPhoto(string $code, int|string $ticketId, Generator $faker): int
    {
        $path = self::photoPlaceholder();
        if ($path === null || ! self::isPhotographed($code)) {
            return 0;
        }

        try {
            /** @var Ticket|null $ticket */
            $ticket = Ticket::query()->find($ticketId);
            if ($ticket === null || ! $ticket->getMedia('ticket')->isEmpty()) {
                return 0;
            }

            $ticket->addMedia($path)
                ->preservingOriginal()
                ->withCustomProperties(['source' => 'fixcity-demo', 'frame' => $faker->numberBetween(1, 3)])
                ->toMediaCollection('ticket');
        } catch (\Throwable) {
            return 0;
        }

        return 1;
    }

    /**
     * Questo ticket porta una foto? Estrazione stabile sul codice: la stessa
     * porzione di ticket e' fotografata a ogni esecuzione.
     */
    private static function isPhotographed(string $code): bool
    {
        $bucket = (int) hexdec(substr(md5($code), 0, 4)) % 1000;

        return $bucket < self::PHOTO_RATE_PERCENT * 10;
    }

    /**
     * Prima immagine utilizzabile come foto di segnalazione, o null se nel tema
     * non resta nessun file che Spatie accetterebbe.
     */
    public static function photoPlaceholder(): ?string
    {
        foreach (self::PHOTO_PLACEHOLDERS as $candidate) {
            $absolute = base_path($candidate);
            if (! File::isFile($absolute)) {
                continue;
            }

            if (in_array(File::mimeType($absolute), self::ACCEPTED_PHOTO_MIMES, true)) {
                return $absolute;
            }
        }

        return null;
    }

    /**
     * Payload geografico nel formato atteso da NormalizeTicketLocationDataAction,
     * che ricava latitude/longitude da `lat`/`lng` durante il salvataggio.
     *
     * @param  TicketPlan  $plan
     * @return array<string, mixed>
     */
    private static function locationPayload(array $plan): array
    {
        return [
            'lat' => $plan['coordinates']['lat'],
            'lng' => $plan['coordinates']['lng'],
            'address' => $plan['address']['address'],
            // `address` contiene gia' il comune: qui si aggiunge solo il paese.
            'display_name' => $plan['address']['address'].', Italia',
            'provider' => 'fixcity-demo',
            'street' => $plan['address']['street'],
            'street_number' => $plan['address']['street_number'],
            'postcode' => $plan['address']['postal_code'],
            'city' => $plan['municipality']['name'],
            'province' => $plan['municipality']['province'],
            'region' => $plan['municipality']['region'],
            'country' => 'Italia',
            'country_code' => 'it',
        ];
    }

    /**
     * Valutazione del cittadino: arriva su pratiche risolte o chiuse, e non sempre.
     * E' il dato che alimenta il widget di soddisfazione in dashboard.
     *
     * @param  TicketPlan  $plan
     * @return array{0: int|null, 1: Carbon|null}
     */
    private function citizenRating(Generator $faker, array $plan): array
    {
        if (! in_array($plan['status'], [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true)) {
            return [null, null];
        }

        $rate = $plan['status'] === TicketStatusEnum::CLOSED
            ? $faker->boolean(78)
            : $faker->boolean(31);

        if (! $rate) {
            return [null, null];
        }

        $roll = $faker->randomFloat(2, 0, 100);
        $rating = match (true) {
            $roll <= 12 => 1,
            $roll <= 26 => 2,
            $roll <= 52 => 3,
            $roll <= 84 => 4,
            default => 5,
        };

        return [$rating, $plan['updated_at']->copy()->addHours($faker->numberBetween(6, 96))];
    }

    /**
     * Istante in cui l'ufficio prende in carico: il sopralluogo se presente,
     * altrimenti il primo passaggio di stato dopo l'apertura.
     *
     * @param  TicketPlan  $plan
     */
    private static function assignmentMoment(array $plan): Carbon
    {
        foreach ([TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::PENDING] as $status) {
            foreach ($plan['milestones'] as $milestone) {
                if ($milestone['status'] === $status) {
                    return $milestone['at']->copy();
                }
            }
        }

        $milestones = $plan['milestones'];

        return $milestones[count($milestones) - 1]['at']->copy();
    }

    /**
     * Rilevamento una sola volta per esecuzione: sono due interrogazioni su
     * information_schema, non una per ogni ticket.
     */
    public static function hasCitizenRatingColumns(): bool
    {
        if (self::$hasCitizenRating !== null) {
            return self::$hasCitizenRating;
        }

        $schema = Schema::connection('fixcity');

        return self::$hasCitizenRating = $schema->hasColumn('tickets', 'citizen_rating')
            && $schema->hasColumn('tickets', 'citizen_rated_at');
    }

    private static function hasActivityTable(): bool
    {
        return Schema::connection('fixcity')->hasTable('ticket_activities');
    }
}

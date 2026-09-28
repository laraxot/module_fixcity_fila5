<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Database\Seeders\Support\DemoCategoryCatalog;
use Modules\Fixcity\Database\Seeders\Support\DemoMunicipalityProvider;
use Modules\Fixcity\Database\Seeders\Support\DemoNarrative;
use Modules\Fixcity\Database\Seeders\Support\DemoPeopleProvider;
use Modules\Fixcity\Database\Seeders\Support\DemoSlaTimeline;
use Modules\Fixcity\Database\Seeders\Support\DemoWeightedChoice;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Segnalazioni demo per la presentazione investitori: 520 ticket in 31 comuni
 * italiani, distribuiti sulle 22 categorie del catalogo, con percorso di stato
 * legale, timeline SLA, assegnazione all'ufficio competente e allegati fotografici.
 *
 * Il dataset e' deterministico (Faker con seed fisso) e idempotente: ogni ticket e'
 * identificato dal `code` DMO-0001..DMO-0520, quindi un secondo lancio aggiorna le
 * stesse righe e lascia invariato il conteggio. Le attivita' dei soli ticket DMO-*
 * vengono rimosse prima di essere riscritte, cosi' la timeline non si duplica.
 *
 * Nota: lo schema `tickets` non prevede `category_id`, quindi la categoria arriva
 * alla segnalazione tramite il campo `type` (TicketTypeEnum), valorizzato dalla
 * categoria scelta dal piano. Ogni categoria del catalogo dichiara la sua `type`.
 *
 * Uso:
 *   php artisan db:seed --class="Modules\Fixcity\Database\Seeders\TicketSeeder"
 *
 * @phpstan-type Milestone array{status: TicketStatusEnum, at: Carbon, payload: array<string, mixed>}
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
class TicketSeeder extends Seeder
{
    /**
     * Prefisso dei `code` generati: non collide con DEMO-* (presentazione FO)
     * ne' con INV-* (campagna investitori preesistente).
     */
    public const string CODE_PREFIX = 'DMO';

    public const int DEFAULT_TICKET_COUNT = 520;

    /**
     * Seed fisso: due esecuzioni producono lo stesso dataset, la demo e' riproducibile.
     */
    public const int RANDOM_SEED = 20260927;

    /**
     * Frazione di ticket che ricevono una foto allegata.
     */
    private const float PHOTO_RATE = 0.12;

    /**
     * Il modello Ticket dichiara `citizen_rating`/`citizen_rated_at`, ma lo schema
     * puo' non averle ancora: quando mancano la demo prosegue senza valutazioni
     * invece di morire su una colonna inesistente.
     */
    private static ?bool $hasCitizenRating = null;

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

    /**
     * @var list<array{0: TicketStatusEnum, 1: int}>
     */
    private const array STATUS_WEIGHTS = [
        [TicketStatusEnum::OPEN, 20],
        [TicketStatusEnum::PENDING, 8],
        [TicketStatusEnum::IN_REVIEW, 6],
        [TicketStatusEnum::IN_PROGRESS, 16],
        [TicketStatusEnum::ON_HOLD, 5],
        [TicketStatusEnum::RESOLVED, 22],
        [TicketStatusEnum::CLOSED, 17],
        [TicketStatusEnum::REOPENED, 3],
        [TicketStatusEnum::DRAFT, 3],
    ];

    /**
     * @var list<array{0: TicketPriorityEnum, 1: int}>
     */
    private const array PRIORITY_WEIGHTS = [
        [TicketPriorityEnum::LOW, 18],
        [TicketPriorityEnum::MEDIUM, 34],
        [TicketPriorityEnum::HIGH, 27],
        [TicketPriorityEnum::CRITICAL, 14],
        [TicketPriorityEnum::URGENT, 7],
    ];

    /**
     * @return list<int|string>
     */
    public function run(): array
    {
        return $this->seedTickets(self::DEFAULT_TICKET_COUNT);
    }

    /**
     * Genera (o riallinea) il dataset delle segnalazioni demo.
     *
     * @return list<int|string> chiavi dei ticket toccati, per i seeder che seguono
     */
    public function seedTickets(int $count, ?int $randomSeed = null): array
    {
        if (! $this->isReady()) {
            return [];
        }

        $faker = Factory::create('it_IT');
        $faker->seed($randomSeed ?? self::RANDOM_SEED);

        $categories = DemoCategoryCatalog::all();
        $citizens = DemoPeopleProvider::citizens($faker);
        if ($citizens === []) {
            $this->command?->warn('TicketSeeder: nessun cittadino disponibile, seed saltato.');

            return [];
        }

        $this->clearDemoActivities();

        $ticketIds = [];
        $photos = 0;

        for ($index = 1; $index <= $count; ++$index) {
            $plan = $this->plan($faker, $index, $categories, $citizens);
            $ticketId = $this->persist($faker, $plan);
            if ($ticketId === null) {
                continue;
            }

            $this->writeActivities($faker, $plan, $ticketId);
            $photos += $this->maybeAttachPhoto($ticketId, $faker);
            $ticketIds[] = $ticketId;
        }

        $this->report($count, $photos);

        return $ticketIds;
    }

    /**
     * Compone il piano di un singolo ticket: tutto in memoria, nessuna scrittura.
     *
     * @param  list<CategoryRow>  $categories
     * @param  non-empty-list<PersonRow>  $citizens
     * @return TicketPlan
     */
    private function plan(Generator $faker, int $index, array $categories, array $citizens): array
    {
        $category = self::weighted($faker, $categories, static fn (array $row): int => $row['weight']);
        $municipality = DemoMunicipalityProvider::pickWeighted($faker);
        $address = DemoMunicipalityProvider::streetAddress($faker, $municipality);
        $coordinates = DemoMunicipalityProvider::nearbyCoordinates($faker, $municipality);
        $status = self::weightedEnum($faker, self::STATUS_WEIGHTS);
        $priority = self::weightedEnum($faker, self::PRIORITY_WEIGHTS);
        $citizen = $citizens[$faker->randomElement(array_keys($citizens))];
        $assignee = DemoPeopleProvider::staffFor($faker, $category['department']);

        $createdAt = self::createdAt($faker);
        $milestones = DemoSlaTimeline::milestones($faker, $createdAt, $status, $priority);
        $lastMilestone = $milestones[count($milestones) - 1];
        $title = str_replace('{street}', $address['street'], $faker->randomElement($category['titles']));
        $detail = str_replace('{street}', $address['street'], $faker->randomElement($category['details']));

        return [
            'code' => self::code($index),
            'slug' => self::slug($index, $title),
            'title' => $title,
            'content' => DemoNarrative::openingReport($faker, $address['street'], $detail)
                ."\n\n".DemoNarrative::priorityRationale($faker, $priority),
            'status' => $status,
            'priority' => $priority,
            'type' => $category['type'],
            'category' => $category,
            'municipality' => $municipality,
            'address' => $address,
            'coordinates' => $coordinates,
            'owner' => $citizen,
            'assignee' => $assignee,
            'responsible_id' => self::responsibleFor($status, $assignee['id']),
            'created_at' => $createdAt,
            'updated_at' => $lastMilestone['at'],
            'estimation' => DemoSlaTimeline::estimatedHours($faker, $priority),
            'milestones' => $milestones,
        ];
    }

    /**
     * L'ufficio riceve il ticket solo dal sopralluogo: aperto e in presa in carico
     * restano senza assegnatario, come nel flusso reale dell'ente.
     */
    private static function responsibleFor(TicketStatusEnum $status, int|string $assigneeId): int|string|null
    {
        return match ($status) {
            TicketStatusEnum::DRAFT, TicketStatusEnum::OPEN, TicketStatusEnum::PENDING => null,
            default => $assigneeId,
        };
    }

    /**
     * Scrive il ticket, i timestamp del modello e la valutazione del cittadino.
     *
     * @param  TicketPlan  $plan
     */
    private function persist(Generator $faker, array $plan): int|string|null
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

        $timestamps = [
            'created_at' => $plan['created_at'],
            'updated_at' => $plan['updated_at'],
            // `ticket_prefix` non e' fra i campi fillable di Ticket: va forzato,
            // altrimenti il prefisso DMO verrebbe scartato in silenzio.
            'ticket_prefix' => self::CODE_PREFIX,
        ];

        if (self::hasCitizenRatingColumns()) {
            [$rating, $ratedAt] = $this->citizenRating($faker, $plan);
            $timestamps['citizen_rating'] = $rating;
            $timestamps['citizen_rated_at'] = $ratedAt;
        }

        $ticket->forceFill($timestamps)->save();

        // HasSlug rigenera lo slug da `name` a ogni salvataggio: lo ripristiniamo
        // perche' l'idempotenza del dataset non dipenda dal titolo scelto.
        Ticket::withoutEvents(static function () use ($ticket, $slug): void {
            Ticket::query()->whereKey($ticket->getKey())->update(['slug' => $slug]);
        });

        return $ticket->getKey();
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
        if (! self::hasCitizenRatingColumns()) {
            return [null, null];
        }

        if (! in_array($plan['status'], [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true)) {
            return [null, null];
        }

        $rate = $plan['status'] === TicketStatusEnum::CLOSED
            ? $faker->boolean(78)
            : $faker->boolean(31);

        if (! $rate) {
            return [null, null];
        }

        $weighted = $faker->randomFloat(2, 0, 100);
        $rating = match (true) {
            $weighted <= 12 => 1,
            $weighted <= 26 => 2,
            $weighted <= 52 => 3,
            $weighted <= 84 => 4,
            default => 5,
        };

        return [$rating, $plan['updated_at']->copy()->addHours($faker->numberBetween(6, 96))];
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
     * Timeline di stato e SLA del ticket. Riparte da zero perche' la chiave di
     * matching (`reason`) e' un testo umano e non puo' garantire l'idempotenza.
     *
     * @param  TicketPlan  $plan
     */
    private function writeActivities(Generator $faker, array $plan, int|string|null $ticketId): void
    {
        if ($ticketId === null || ! Schema::connection('fixcity')->hasTable('ticket_activities')) {
            return;
        }

        foreach ($plan['milestones'] as $position => $milestone) {
            $from = $position === 0 ? null : $plan['milestones'][$position - 1]['status'];

            $activity = TicketActivity::query()->create([
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
            ]);

            $activity->forceFill([
                'created_at' => $milestone['at'],
                'updated_at' => $milestone['at'],
            ])->save();
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

        $activity = TicketActivity::query()->create([
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
        ]);

        $assignedAt = self::assignmentMoment($plan);
        $activity->forceFill(['created_at' => $assignedAt, 'updated_at' => $assignedAt])->save();
    }

    /**
     * Istante in cui l'ufficio prende in carico: il sopralluogo se presente,
     * altrimenti il primo passaggio di stato dopo l'apertura.
     *
     * @param  TicketPlan  $plan
     */
    private static function assignmentMoment(array $plan): Carbon
    {
        $preferred = [TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::PENDING];

        foreach ($preferred as $status) {
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
     * Rimuove la timeline dei soli ticket DMO-*: le attivita' degli altri seeder
     * (DEMO-*, INV-*) non le tocca.
     */
    private function clearDemoActivities(): void
    {
        if (! Schema::connection('fixcity')->hasTable('ticket_activities')) {
            return;
        }

        $ticketIds = self::demoTickets()->pluck('id')->all();
        if ($ticketIds !== []) {
            TicketActivity::query()->whereIn('ticket_id', $ticketIds)->forceDelete();
        }
    }

    /**
     * Allega una foto di segnalazione. Senza file sul disco, o senza il supporto
     * media, la demo resta comunque leggibile: il fallimento non e' fatale.
     */
    private function maybeAttachPhoto(int|string $ticketId, Generator $faker): int
    {
        $path = $this->photoPlaceholder();
        if ($path === null || ! $faker->boolean(self::PHOTO_RATE)) {
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
        } catch (\Throwable $exception) {
            $this->command?->warn('TicketSeeder: allegato non riuscito per ticket '.$ticketId.' — '.$exception->getMessage());

            return 0;
        }

        return 1;
    }

    /**
     * Prima immagine utilizzabile come foto di segnalazione, o null se nel tema
     * non resta nessun file che Spatie accetterebbe.
     */
    private function photoPlaceholder(): ?string
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
     * Rilevamento una sola volta per esecuzione: sono due interrogazioni su
     * information_schema, non una per ogni ticket.
     */
    private static function hasCitizenRatingColumns(): bool
    {
        if (self::$hasCitizenRating !== null) {
            return self::$hasCitizenRating;
        }

        $schema = Schema::connection('fixcity');

        return self::$hasCitizenRating = $schema->hasColumn('tickets', 'citizen_rating')
            && $schema->hasColumn('tickets', 'citizen_rated_at');
    }

    /**
     * I soli ticket generati da questo seeder: il prefisso e' nel `code`, unico
     * campo realmente fillable, per non sfiorare DEMO-* e INV-*.
     *
     * @return \Illuminate\Database\Eloquent\Builder<Ticket>
     */
    private static function demoTickets(): Builder
    {
        return Ticket::query()->where('code', 'like', self::CODE_PREFIX.'-%');
    }

    private function isReady(): bool
    {
        $schema = Schema::connection('fixcity');
        foreach (['tickets', 'categories'] as $table) {
            if (! $schema->hasTable($table)) {
                $this->command?->warn('TicketSeeder: tabella '.$table.' non presente, seed saltato.');

                return false;
            }
        }

        return true;
    }

    private function report(int $count, int $photos): void
    {
        $tickets = self::demoTickets()->count();
        $this->command?->info(sprintf(
            'TicketSeeder: %d ticket DMO in %d comuni italiani, %d allegati, %d attivita\' di stato.',
            $tickets,
            count(DemoMunicipalityProvider::all()),
            $photos,
            TicketActivity::query()->whereIn('ticket_id', self::demoTickets()->select('id'))->count(),
        ));

        if ($tickets < $count) {
            $this->command?->warn('TicketSeeder: attesi '.$count.' ticket, trovati '.$tickets.'.');
        }
    }

    /**
     * Eta' del ticket: distribuzione sbilanciata verso il recente, come in una
     * piattaforma in crescita. Range 0-400 giorni.
     */
    private static function createdAt(Generator $faker): Carbon
    {
        $days = (int) round(400 * (1 - $faker->randomFloat(4, 0, 1) ** 0.5));

        return Carbon::now()
            ->subDays($days)
            ->subHours($faker->numberBetween(6, 23))
            ->subMinutes($faker->numberBetween(0, 59));
    }

    private static function code(int $index): string
    {
        return self::CODE_PREFIX.'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Slug univoco: il codice del ticket e' l'unico che garantisce l'unicita',
     * quindi viene riservato lo spazio finale necessario.
     */
    private static function slug(int $index, string $title): string
    {
        $suffix = '_'.self::code($index);
        $base = trim((string) preg_replace('/[^a-z0-9]+/u', '_', mb_strtolower($title, 'UTF-8')), '_');
        $base = substr($base !== '' ? $base : 'segnalazione', 0, 50 - strlen($suffix));

        return $base.$suffix;
    }

    /**
     * Estrae un elemento a peso (le categorie non hanno tutte la stessa frequenza).
     *
     * @template T of array<string, mixed>
     *
     * @param  non-empty-list<T>  $rows
     * @param  callable(T, int): int  $weight
     * @return T
     */
    private static function weighted(Generator $faker, array $rows, callable $weight): array
    {
        $weights = array_map($weight, $rows, array_keys($rows));

        return DemoWeightedChoice::of($faker, $rows, $weights);
    }

    /**
     * @template T of TicketStatusEnum|TicketPriorityEnum
     *
     * @param  non-empty-list<array{0: T, 1: int}>  $pairs
     * @return T
     */
    private static function weightedEnum(Generator $faker, array $pairs): TicketStatusEnum|TicketPriorityEnum
    {
        $weights = array_map(static fn (array $pair): int => $pair[1], $pairs);

        return DemoWeightedChoice::of($faker, $pairs, $weights)[0];
    }
}

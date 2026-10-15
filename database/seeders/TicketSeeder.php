<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Database\Seeders\Support\DemoCategoryCatalog;
use Modules\Fixcity\Database\Seeders\Support\DemoMunicipalityProvider;
use Modules\Fixcity\Database\Seeders\Support\DemoNarrative;
use Modules\Fixcity\Database\Seeders\Support\DemoPeopleProvider;
use Modules\Fixcity\Database\Seeders\Support\DemoSlaTimeline;
use Modules\Fixcity\Database\Seeders\Support\DemoText;
use Modules\Fixcity\Database\Seeders\Support\DemoTicketWriter;
use Modules\Fixcity\Database\Seeders\Support\DemoWeightedChoice;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketActivity;

/**
 * Segnalazioni demo per la presentazione investitori: 520 ticket in 31 comuni
 * italiani, distribuiti sulle 21 categorie del catalogo, con percorso di stato
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
 * @phpstan-import-type TicketPlan from Support\DemoTicketWriter
 * @phpstan-import-type CategoryRow from Support\DemoTicketWriter
 * @phpstan-import-type PersonRow from Support\DemoTicketWriter
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

        $citizens = DemoPeopleProvider::citizens($faker);
        if ($citizens === []) {
            $this->command?->warn('TicketSeeder: nessun cittadino disponibile, seed saltato.');

            return [];
        }

        $writer = new DemoTicketWriter(self::CODE_PREFIX);
        $writer->clearDemoActivities();

        /** @var non-empty-list<CategoryRow> $categories */
        $categories = DemoCategoryCatalog::all();
        $ticketIds = [];
        $photos = 0;

        for ($index = 1; $index <= $count; $index++) {
            $plan = $this->plan($faker, $index, $categories, $citizens);
            $ticketId = $writer->persist($faker, $plan);
            $writer->writeActivities($faker, $plan, $ticketId);
            $photos += $writer->maybeAttachPhoto($plan['code'], $ticketId, $faker);
            $ticketIds[] = $ticketId;
        }

        $this->report($count, $photos, $ticketIds);

        return $ticketIds;
    }

    /**
     * Compone il piano di un singolo ticket: tutto in memoria, nessuna scrittura.
     *
     * @param  non-empty-list<CategoryRow>  $categories
     * @param  non-empty-list<PersonRow>  $citizens
     * @return TicketPlan
     */
    private function plan(Generator $faker, int $index, array $categories, array $citizens): array
    {
        $category = self::weighted($faker, $categories, static fn (array $row): int => $row['weight']);
        $municipality = DemoMunicipalityProvider::pickWeighted($faker);
        $address = DemoMunicipalityProvider::streetAddress($faker, $municipality);
        $status = self::weightedEnum($faker, self::STATUS_WEIGHTS);
        $priority = self::weightedEnum($faker, self::PRIORITY_WEIGHTS);
        $citizen = $citizens[DemoWeightedChoice::index($faker, array_fill(0, count($citizens), 1))];
        $assignee = DemoPeopleProvider::staffFor($faker, $category['department']);

        $createdAt = self::createdAt($faker);
        $milestones = DemoSlaTimeline::milestones($faker, $createdAt, $status, $priority);
        $title = str_replace('{street}', $address['street'], DemoText::pick($faker, $category['titles']));
        $detail = str_replace('{street}', $address['street'], DemoText::pick($faker, $category['details']));

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
            'coordinates' => DemoMunicipalityProvider::nearbyCoordinates($faker, $municipality),
            'owner' => $citizen,
            'assignee' => $assignee,
            'responsible_id' => self::responsibleFor($status, $assignee['id']),
            'created_at' => $createdAt,
            'updated_at' => $milestones[count($milestones) - 1]['at'],
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
     * @param  list<int|string>  $ticketIds
     */
    private function report(int $expected, int $photos, array $ticketIds): void
    {
        $tickets = (new DemoTicketWriter(self::CODE_PREFIX))->demoTickets()->count();
        $this->command?->info(sprintf(
            'TicketSeeder: %d ticket DMO in %d comuni italiani, %d allegati, %d attivita\' di stato.',
            $tickets,
            count(DemoMunicipalityProvider::all()),
            $photos,
            TicketActivity::query()->whereIn('ticket_id', $ticketIds)->count(),
        ));

        if ($tickets < $expected) {
            $this->command?->warn('TicketSeeder: attesi '.$expected.' ticket, trovati '.$tickets.'.');
        }
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

    /**
     * @return non-empty-string
     */
    private static function code(int $index): string
    {
        return self::CODE_PREFIX.'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Slug univoco: il codice del ticket e' l'unico che garantisce l'unicita',
     * quindi viene riservato lo spazio finale necessario.
     *
     * @return non-empty-string
     */
    private static function slug(int $index, string $title): string
    {
        $suffix = '_'.self::code($index);

        return DemoText::slugify($title, 50 - strlen($suffix)).$suffix;
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
        return DemoWeightedChoice::of($faker, $rows, array_map($weight, $rows, array_keys($rows)));
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

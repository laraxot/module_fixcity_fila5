<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Fixcity\Database\Seeders\Support\DemoCategoryCatalog;
use Modules\Fixcity\Database\Seeders\Support\DemoMunicipalityProvider;
use Modules\Fixcity\Database\Seeders\Support\DemoPeopleProvider;
use Modules\Fixcity\Models\Category;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;

/**
 * Orchestratore della demo investitori.
 *
 * Un solo comando produce il quadro completo: account di accesso, categorie,
 * 520 segnalazioni in 31 comuni italiani con foto e SLA, discussioni e ore di
 * cantiere. L'ordine e' vincolato (people -> categorie -> ticket -> commenti ->
 * ore) e ogni passo e' idempotente, quindi il comando puo' essere rilanciato
 * senza duplicare nulla.
 *
 * Uso:
 *   php artisan db:seed --class="Modules\Fixcity\Database\Seeders\FixcityDemoSeeder"
 *
 * Fuori dagli ambienti local/testing/demo non viene scritto niente: la demo non
 * deve mai inquinare un database reale.
 */
class FixcityDemoSeeder extends Seeder
{
    public const int DEFAULT_TICKET_COUNT = TicketSeeder::DEFAULT_TICKET_COUNT;

    /**
     * @var list<string>
     */
    private const array ALLOWED_ENVIRONMENTS = ['local', 'testing', 'demo'];

    public function run(): void
    {
        if (! $this->isAllowedEnvironment()) {
            $this->command?->warn('FixcityDemoSeeder: ambiente non ammesso, nessun dato scritto.');

            return;
        }

        $started = microtime(true);
        $faker = Factory::create('it_IT');
        $faker->seed(TicketSeeder::RANDOM_SEED);

        $this->step('1/6 Account demo', function (): void {
            // Gli account con cui si fa login (cittadino, operatore, admin) devono
            // esistere prima di tutto: sono il riferimento per il resto del dataset.
            $this->call(DemoUsersSeeder::class);
            $this->call(DemoOperatorPanelAccessSeeder::class);
        });

        $this->step('2/6 Utenze demo', function () use ($faker): void {
            $citizens = DemoPeopleProvider::citizens($faker);
            $staff = DemoPeopleProvider::staff($faker);

            $this->command?->info(sprintf(
                '  %d cittadini segnalatori, %d operatori su %d uffici comunali.',
                count($citizens),
                count($staff),
                count(DemoCategoryCatalog::DEPARTMENTS),
            ));
        });

        $this->step('3/6 Categorie', function (): void {
            $this->call(CategorySeeder::class);
        });

        $ticketIds = [];
        $this->step('4/6 Segnalazioni', function () use (&$ticketIds): void {
            $ticketIds = $this->seeder(TicketSeeder::class)->seedTickets(self::DEFAULT_TICKET_COUNT);
        });

        $commentCount = 0;
        $this->step('5/6 Commenti', function () use (&$ticketIds, &$commentCount): void {
            $commentCount = $this->seeder(TicketCommentSeeder::class)->seedDatasetComments($ticketIds);
        });

        $hourCount = 0;
        $this->step('6/6 Ore lavorate', function () use (&$ticketIds, &$hourCount): void {
            $hourCount = $this->seeder(TicketHourSeeder::class)->seedDatasetHours($ticketIds);
        });

        $this->summary($ticketIds, $commentCount, $hourCount, microtime(true) - $started);
    }

    /**
     * Istanzia il seeder dal container e gli inoltra l'output: risolto dal container
     * il seeder non ha un command, quindi i suoi messaggi andrebbero persi.
     *
     * @template T of Seeder
     *
     * @param  class-string<T>  $seeder
     * @return T
     */
    private function seeder(string $seeder): Seeder
    {
        $instance = app($seeder);
        if (! $instance instanceof Seeder) {
            throw new \RuntimeException("Il seeder {$seeder} non e' risolvibile dal container.");
        }

        /** @var T $instance */
        $instance->setCommand($this->command);

        return $instance;
    }

    private function isAllowedEnvironment(): bool
    {
        return app()->environment(self::ALLOWED_ENVIRONMENTS);
    }

    private function step(string $label, callable $callback): void
    {
        $this->command?->info('');
        $this->command?->info('==> '.$label);
        $callback();
    }

    /**
     * @param  list<int|string>  $ticketIds
     */
    private function summary(array $ticketIds, int $commentCount, int $hourCount, float $seconds): void
    {
        $municipalities = count(DemoMunicipalityProvider::all());
        $categories = Category::query()->count();
        $tickets = Ticket::query()->where('code', 'like', TicketSeeder::CODE_PREFIX.'-%')->count();
        $photos = Ticket::query()
            ->where('code', 'like', TicketSeeder::CODE_PREFIX.'-%')
            ->with('media')
            ->get()
            ->sum(static fn (Ticket $ticket): int => $ticket->media->count());

        $lines = [
            'Fixcity demo pronta in '.number_format($seconds, 1).' s.',
            '  categorie         '.$categories,
            '  ticket            '.$tickets.' in '.$municipalities.' comuni italiani',
            '  allegati          '.$photos,
            '  attivita di stato '.TicketActivity::query()->whereIn('ticket_id', $ticketIds)->count(),
            '  commenti          '.$commentCount,
            '  ore lavorate      '.$hourCount,
        ];

        foreach ($lines as $line) {
            $this->command?->info($line);
        }

        $this->command?->info('');
        $this->command?->info('  Account demo: operatore@fixcity.demo / cittadino@fixcity.demo (password: password)');
    }
}

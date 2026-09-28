<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Database\Seeders\Support\DemoNarrative;
use Modules\Fixcity\Database\Seeders\Support\DemoPeopleProvider;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Activity;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketHour;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;

/**
 * Ore lavorate sulle segnalazioni: e' il dato che trasforma una lista di ticket in
 * un cruscotto (costo per ufficio, ore per tipologia, carico per operatore).
 *
 * Copre due dataset:
 *
 * - le 27 segnalazioni della presentazione FO: le tre voci di triage storiche;
 * - il dataset investitori DMO-*: ore sugli operatori dell'ufficio competente,
 *   ancorate ai momenti in cui la pratica e' stata effettivamente lavorata e
 *   coerenti con la stima (`estimation`) dichiarata sul ticket.
 */
class TicketHourSeeder extends Seeder
{
    public const int RANDOM_SEED = 20260929;

    /**
     * Ore per intervento: squadre da cantiere, non singoli minuti.
     *
     * @var array{0: float, 1: float}
     */
    private const array HOURS_PER_ENTRY = [0.5, 7.5];

    /**
     * Quante voci di ore genera una pratica lavorata sul campo.
     *
     * @var array{0: int, 1: int}
     */
    private const array ENTRIES_PER_TICKET = [1, 4];

    public function run(): void
    {
        $this->seedPresentationHours();
        $this->seedDatasetHours();
    }

    /**
     * Voci di triage sulle segnalazioni della presentazione FO.
     */
    public function seedPresentationHours(): void
    {
        if (! $this->hoursTableIsUsable()) {
            return;
        }

        $userClass = XotData::make()->getUserClass();
        $userId = $userClass::query()->orderBy('id')->value('id');
        $activityId = Activity::query()->orderBy('id')->value('id');
        if ($userId === null) {
            return;
        }

        $tickets = Ticket::query()->where('code', 'like', 'DEMO-%')->orderBy('id')->limit(3)->get();
        foreach ($tickets as $ticket) {
            TicketHour::query()->updateOrCreate(
                ['ticket_id' => $ticket->getKey(), 'user_id' => $userId, 'comment' => 'Demo triage time'],
                ['value' => 1.5, 'activity_id' => $activityId],
            );
        }

        if ($tickets->isNotEmpty()) {
            $this->command?->info('TicketHourSeeder: ore di triage su '.$tickets->count().' ticket FO.');
        }
    }

    /**
     * Ore di cantiere sul dataset investitori DMO-*.
     *
     * @param  list<int|string>|null  $ticketIds se null, prende tutti i ticket DMO-*
     * @return int numero di voci scritte
     */
    public function seedDatasetHours(?array $ticketIds = null, int $randomSeed = self::RANDOM_SEED): int
    {
        if (! $this->hoursTableIsUsable()) {
            return 0;
        }

        $query = Ticket::query()->where('code', 'like', TicketSeeder::CODE_PREFIX.'-%');
        if ($ticketIds !== null && $ticketIds !== []) {
            $query->whereIn('id', $ticketIds);
        }

        $tickets = $query->with('activities')->get()->filter(
            static fn (Ticket $ticket): bool => $ticket->activities->isNotEmpty(),
        );
        if ($tickets->isEmpty()) {
            return 0;
        }

        $faker = Factory::create('it_IT');
        $faker->seed($randomSeed);

        $activityIds = $this->activityIds();
        $staff = DemoPeopleProvider::staff($faker);

        TicketHour::query()->whereIn('ticket_id', $tickets->modelKeys())->forceDelete();

        $written = 0;
        foreach ($tickets as $ticket) {
            $written += $this->writeTicketHours($faker, $ticket, $staff, $activityIds);
        }

        return $written;
    }

    /**
     * Voci di ore per una pratica: distribuite sui momenti in cui la squadra ha
     * lavorato, con un totale che rispetta la stima dichiarata sul ticket.
     *
     * @param  list<array{id: int|string, email: non-empty-string, department: non-empty-string|null}>  $staff
     * @param  list<int>  $activityIds
     */
    private function writeTicketHours(Generator $faker, Ticket $ticket, array $staff, array $activityIds): int
    {
        $workMoments = $this->workMoments($ticket);
        if ($workMoments === []) {
            return 0;
        }

        $assignee = $this->assigneeFor($ticket, $staff);
        if ($assignee === null) {
            return 0;
        }

        [$min, $max] = self::ENTRIES_PER_TICKET;
        $entries = min($faker->numberBetween($min, $max), count($workMoments));
        $moments = array_slice($workMoments, 0, $entries);

        $remaining = max(0.5, (float) $ticket->estimation);
        $written = 0;

        foreach ($moments as $position => $moment) {
            $isLast = $position === count($moments) - 1;
            $share = $isLast
                ? $remaining
                : round($remaining / (count($moments) - $position) * $faker->randomFloat(2, 0.7, 1.3), 2);

            // Una voce non puo' durare meno di mezz'ora ne' eccedere la giornata:
            // una riga da 40 ore in tabella non e' credibile a una demo.
            [$minHours, $maxHours] = self::HOURS_PER_ENTRY;
            $value = min($remaining, min($maxHours, max($minHours, $share)));
            $remaining = max(0.0, $remaining - $value);

            $this->writeHour($ticket, $assignee['id'], $moment, $value, $faker, $activityIds);
            $written++;
        }

        return $written;
    }

    private function writeHour(
        Ticket $ticket,
        int|string $assigneeId,
        Carbon $at,
        float $value,
        Generator $faker,
        array $activityIds,
    ): void {
        /** @var TicketHour $hour */
        $hour = TicketHour::query()->create([
            'ticket_id' => $ticket->getKey(),
            'user_id' => $assigneeId,
            'activity_id' => $faker->randomElement($activityIds),
            'value' => $value,
            'comment' => DemoNarrative::hourNote($faker),
        ]);

        $hour->forceFill(['created_at' => $at, 'updated_at' => $at])->save();
    }

    /**
     * Momenti in cui la pratica e' stata lavorata: dal sopralluogo in avanti.
     *
     * @return list<Carbon>
     */
    private function workMoments(Ticket $ticket): array
    {
        $moments = [];

        foreach ($ticket->activities->sortBy('created_at') as $activity) {
            if (! $activity->created_at instanceof Carbon) {
                continue;
            }

            $payload = $activity->payload ?? [];
            $status = is_string($payload['to'] ?? null) ? TicketStatusEnum::tryFrom($payload['to']) : null;
            if ($status === null || in_array($status, [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::DRAFT], true)) {
                continue;
            }

            $moments[] = $activity->created_at;
        }

        return $this->uniqueMoments($moments);
    }

    /**
     * @param  list<Carbon>  $moments
     * @return list<Carbon>
     */
    private function uniqueMoments(array $moments): array
    {
        $unique = [];
        foreach ($moments as $moment) {
            $unique[$moment->toIso8601String()] = $moment;
        }

        return array_values($unique);
    }

    /**
     * Chi ha firmato le ore: l'operatore assegnato, altrimenti uno del pool.
     *
     * @param  list<array{id: int|string, email: non-empty-string, department: non-empty-string|null}>  $staff
     * @return array{id: int|string, email: non-empty-string, department: non-empty-string|null}|null
     */
    private function assigneeFor(Ticket $ticket, array $staff): ?array
    {
        $responsible = SafeStringCastAction::cast($ticket->responsible_id);
        foreach ($staff as $record) {
            if ((string) $record['id'] === $responsible) {
                return $record;
            }
        }

        return $staff[0] ?? null;
    }

    /**
     * Catalogo `activities` usato dal TicketHour: le righe mancanti vengono create.
     *
     * @return list<int>
     */
    private function activityIds(): array
    {
        $ids = [];
        foreach (DemoNarrative::ACTIVITY_TYPES as $type) {
            $activity = Activity::query()->firstOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']],
            );

            $id = $activity->getKey();
            if (is_int($id) || (is_string($id) && $id !== '')) {
                $ids[] = (int) $id;
            }
        }

        return $ids;
    }

    /**
     * `ticket_hours.user_id` e' un uuid: su database legacy con intero la tabella
     * non e' utilizzabile e il seed va saltato invece di scrivere righe incomparabili.
     */
    private function hoursTableIsUsable(): bool
    {
        $model = new TicketHour;
        $connection = $model->getConnectionName();
        $schema = Schema::connection($connection);

        if (! $schema->hasTable($model->getTable())) {
            $this->command?->warn('TicketHourSeeder: tabella non migrata, saltato.');

            return false;
        }

        if (in_array($schema->getColumnType($model->getTable(), 'user_id'), ['bigint', 'integer', 'int'], true)) {
            $this->command?->warn('TicketHourSeeder: user_id legacy intero incompatibile con utenti UUID, saltato.');

            return false;
        }

        return true;
    }
}

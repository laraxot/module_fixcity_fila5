<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Modules\Comment\Models\Comment;
use Modules\Fixcity\Database\Seeders\Support\DemoNarrative;
use Modules\Fixcity\Database\Seeders\Support\DemoPeopleProvider;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;

/**
 * Commenti demo per i ticket — attiva il sistema di discussione.
 *
 * Due dataset distinti:
 *
 * - le 27 segnalazioni della presentazione FO (DEMO-*, VEN-*, LOM-*, ...): un paio di
 *   messaggi per ticket, come da sempre, con i due account demo;
 * - il dataset investitori DMO-* generato da TicketSeeder: discussione realistica,
 *   alternata fra cittadino segnalatore e operatore dell'ufficio competente, con
 *   risposte annidate e orari allineati alla timeline di stato.
 *
 * @phpstan-type Turn array{at: Carbon, status: TicketStatusEnum, author: int|string, text: string, parent: int|null}
 */
class TicketCommentSeeder extends Seeder
{
    public const int RANDOM_SEED = 20260928;

    /**
     * Quanti messaggi per stato raggiunto: piu' la pratica avanza, piu' si parla.
     *
     * @var array<string, array{0: int, 1: int}>
     */
    private const array TURNS_BY_STATUS = [
        'draft' => [0, 0],
        'open' => [1, 2],
        'pending' => [1, 2],
        'in_review' => [2, 3],
        'in_progress' => [3, 4],
        'on_hold' => [2, 3],
        'resolved' => [2, 4],
        'closed' => [1, 2],
        'reopened' => [3, 4],
    ];

    /**
     * @var array<string, list<array{status: string, body: string}>>
     */
    private const array COMMENTS_BY_STATUS = [
        'open' => [
            [
                'status' => 'public',
                'body' => 'Segnalazione ricevuta, stiamo verificando la posizione.',
            ],
            [
                'status' => 'public',
                'body' => 'Grazie per la segnalazione, la task è stata assegnata al team.',
            ],
        ],
        'in_progress' => [
            [
                'status' => 'public',
                'body' => 'Intervento in corso, il team è sul posto.',
            ],
            [
                'status' => 'public',
                'body' => 'Aggiornamento: lavori previsti per domani mattina.',
            ],
        ],
        'resolved' => [
            [
                'status' => 'public',
                'body' => 'Intervento completato con successo. Grazie per la pazienza.',
            ],
        ],
        'on_hold' => [
            [
                'status' => 'public',
                'body' => 'Segnalazione in attesa di approvazione budget/permessi.',
            ],
        ],
    ];

    public function run(): void
    {
        $this->seedPresentationComments();
        $this->seedDatasetComments();
    }

    /**
     * Messaggi sulle 27 segnalazioni della presentazione FO (DEMO-*, VEN-*, ...).
     */
    public function seedPresentationComments(): void
    {
        $tickets = $this->presentationTickets()->get();
        if ($tickets->isEmpty()) {
            $this->command?->warn('TicketCommentSeeder: nessun ticket demo FO, commenti presentazione saltati.');

            return;
        }

        /** @var class-string<User> $citizenClass */
        $citizenClass = XotData::make()->getUserClass();
        $operatorId = $citizenClass::query()->where('email', DemoUsersSeeder::OPERATOR_EMAIL)->value('id');
        $citizenId = $citizenClass::query()->where('email', DemoUsersSeeder::CITIZEN_EMAIL)->value('id');

        if ($operatorId === null || $citizenId === null) {
            $this->command?->warn('TicketCommentSeeder: utenti demo assenti — saltato.');

            return;
        }

        foreach ($tickets as $ticket) {
            $status = $ticket->status instanceof TicketStatusEnum ? $ticket->status : TicketStatusEnum::OPEN;
            $comments = self::COMMENTS_BY_STATUS[$status->value] ?? self::COMMENTS_BY_STATUS['open'];

            foreach ($comments as $index => $commentData) {
                $this->writeComment(
                    (int) $ticket->getKey(),
                    $index % 2 === 0 ? $operatorId : $citizenId,
                    $commentData['body'],
                    $ticket->created_at instanceof Carbon ? $ticket->created_at->copy() : Carbon::now(),
                    null,
                    ['source' => 'demo', 'status' => $commentData['status']],
                );
            }
        }

        $this->command?->info('TicketCommentSeeder: commenti demo creati per '.$tickets->count().' ticket.');
    }

    /**
     * Discussione del dataset investitori DMO-*.
     *
     * @param  list<int|string>|null  $ticketIds se null, prende tutti i ticket DMO-*
     * @return int numero di messaggi scritti
     */
    public function seedDatasetComments(?array $ticketIds = null, int $randomSeed = self::RANDOM_SEED): int
    {
        if (! Schema::connection('fixcity')->hasTable('comments')) {
            $this->command?->warn('TicketCommentSeeder: tabella comments non migrata, saltato.');

            return 0;
        }

        $query = Ticket::query()->where('code', 'like', TicketSeeder::CODE_PREFIX.'-%');
        if ($ticketIds !== null && $ticketIds !== []) {
            $query->whereIn('id', $ticketIds);
        }

        $tickets = $query->with('activities')->get();
        if ($tickets->isEmpty()) {
            $this->command?->warn('TicketCommentSeeder: nessun ticket DMO, commenti saltati.');

            return 0;
        }

        $faker = Factory::create('it_IT');
        $faker->seed($randomSeed);

        // I messaggi precedenti vanno rimossi: senza una chiave naturale stabile
        // un secondo lancio duplicherebbe la discussione.
        Comment::query()
            ->where('commentable_type', Ticket::class)
            ->whereIn('commentable_id', $tickets->modelKeys())
            ->forceDelete();

        $written = 0;
        foreach ($tickets as $ticket) {
            $written += $this->writeTicketDiscussion($faker, $ticket);
        }

        return $written;
    }

    /**
     * Alterna i messaggi fra cittadino e operatore, ancorandoli alla timeline di
     * stato: il primo intervento dell'ente arriva quando l'ufficio prende in carico.
     *
     * @return int numero di messaggi scritti
     */
    private function writeTicketDiscussion(Generator $faker, Ticket $ticket): int
    {
        $status = $ticket->status instanceof TicketStatusEnum ? $ticket->status : TicketStatusEnum::OPEN;
        [$min, $max] = self::TURNS_BY_STATUS[$status->value] ?? [1, 2];
        $turns = $faker->numberBetween($min, $max);
        if ($turns === 0) {
            return 0;
        }

        $moments = $this->momentsOf($ticket);
        if ($moments === []) {
            return 0;
        }

        $citizenId = SafeStringCastAction::cast($ticket->owner_id);
        $staffId = $ticket->responsible_id;
        $written = 0;
        $parentId = null;

        foreach (range(0, $turns - 1) as $position) {
            $moment = $moments[min($position, count($moments) - 1)];
            $isStaff = $position % 2 === 1;
            $author = $isStaff ? ($staffId ?? DemoPeopleProvider::staffFor($faker, 'Servizio Sicurezza Urbana')['id']) : $citizenId;
            $text = $isStaff
                ? DemoNarrative::staffComment($faker, $moment['status'])
                : DemoNarrative::citizenComment($faker, $moment['status']);

            $commentId = $this->writeComment(
                (int) $ticket->getKey(),
                $author,
                $text,
                $moment['at'],
                // Circa un terzo delle discussioni ha una risposta annidata: e' il
                // modo in cui si legge una vera conversazione in una coda di lavoro.
                $position >= 2 && $parentId !== null && $faker->boolean(34) ? $parentId : null,
                ['source' => 'fixcity-demo', 'role' => $isStaff ? 'staff' : 'citizen', 'status' => $moment['status']->value],
            );

            if ($commentId === null) {
                continue;
            }

            $written++;
            $parentId = $commentId;
        }

        return $written;
    }

    /**
     * Istanti della timeline con lo stato raggiunto: i messaggi seguono il ticket.
     *
     * @return list<array{at: Carbon, status: TicketStatusEnum}>
     */
    private function momentsOf(Ticket $ticket): array
    {
        $moments = [[
            'at' => $ticket->created_at instanceof Carbon ? $ticket->created_at : Carbon::now(),
            'status' => TicketStatusEnum::OPEN,
        ]];

        foreach ($ticket->activities->sortBy('created_at') as $activity) {
            $payload = $activity->payload ?? [];
            $to = is_string($payload['to'] ?? null) ? $payload['to'] : null;
            $status = $to === null ? null : TicketStatusEnum::tryFrom($to);
            if ($status === null || ! $activity->created_at instanceof Carbon) {
                continue;
            }

            $moments[] = ['at' => $activity->created_at, 'status' => $status];
        }

        return $moments;
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function writeComment(
        int $ticketId,
        int|string $authorId,
        string $text,
        Carbon $at,
        ?int $parentId,
        array $extra,
    ): ?int {
        $commentatorClass = XotData::make()->getUserClass();

        /** @var Comment $comment */
        $comment = Comment::query()->create([
            'commentable_type' => Ticket::class,
            'commentable_id' => $ticketId,
            'commentator_type' => $commentatorClass,
            'commentator_id' => $authorId,
            'original_text' => $text,
            'parent_id' => $parentId,
            'approved_at' => $at,
            'extra' => $extra,
        ]);

        $comment->forceFill(['created_at' => $at, 'updated_at' => $at])->save();

        return (int) $comment->getKey();
    }

    /**
     * @return Builder<Ticket>
     */
    private function presentationTickets(): Builder
    {
        return Ticket::query()->where(static function (Builder $query): void {
            foreach (['DEMO', 'VEN', 'LOM', 'TOS', 'LAZ', 'CAM', 'PIE', 'EMI'] as $prefix) {
                $query->orWhere('code', 'LIKE', $prefix.'-%');
            }
        });
    }
}

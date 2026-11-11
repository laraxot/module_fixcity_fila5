<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DB-03 / DB-04: indici della migrazione `add_query_indexes_to_tickets_table`. Il controllo e' sulle colonne (un indice equivalente con
 * altro nome conta), come fa ensureIndexes.
 */
it('has the indexes the ticket queries rely on', function (string $model, array $columns): void {
    $instance = new $model;
    Assert::assertInstanceOf(Model::class, $instance);
    $columns = array_values(array_map(static fn (mixed $column): string => SafeStringCastAction::cast($column), $columns));
    $table = $instance->getTable();
    $schema = Schema::connection($instance->getConnectionName());

    Assert::assertTrue($schema->hasIndex($table, $columns), 'Indice mancante su '.$table.'('.implode(', ', $columns).')');
})->with([
    'tickets status + created_at' => [Ticket::class, ['status', 'created_at']],
    'tickets type + status' => [Ticket::class, ['type', 'status']],
    'tickets owner_id + created_at' => [Ticket::class, ['owner_id', 'created_at']],
    'tickets responsible_id' => [Ticket::class, ['responsible_id']],
    'tickets created_at' => [Ticket::class, ['created_at']],
    'tickets code' => [Ticket::class, ['code']],
    'ticket_activities ticket_id + created_at' => [TicketActivity::class, ['ticket_id', 'created_at']],
]);

it('refuses two tickets with the same tracking code when the code index is unique', function (): void {
    $ticket = new Ticket;
    if (! Schema::connection($ticket->getConnectionName())->hasIndex($ticket->getTable(), ['code'], 'unique')) {
        $this->markTestSkipped('tickets.code ha un indice semplice: il database aveva codici duplicati (vedi la migrazione).');
    }

    TicketFactory::new()->createOne(['code' => 'TCK-DB04-DUPLICATO']);

    expect(fn () => TicketFactory::new()->createOne(['code' => 'TCK-DB04-DUPLICATO']))
        ->toThrow(UniqueConstraintViolationException::class);
});

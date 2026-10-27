<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketComment;

/**
 * Seed demo FO: 5 segnalazioni con timeline allineata al workflow reale.
 *
 * Gli stati usati sono solo case di {@see TicketStatusEnum}
 * (PENDING → IN_REVIEW → IN_PROGRESS → CLOSED). Non esistono Approved/Assigned:
 * l'approvazione moderatore e l'assegnazione ufficio sono eventi di attività,
 * non stati del ticket.
 */
class DemoTicketsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Collection<int, Ticket> $tickets */
        $tickets = TicketFactory::new()
            ->count(5)
            ->sequence(
                [
                    'name' => 'Danneggiamento panchina pubblica',
                    'content' => 'La panchina in via Roma è rotta, segnalazione per riparazione.',
                    'type_id' => 'street_furniture',
                    'priority_id' => 'low',
                    'status_id' => TicketStatusEnum::PENDING->value,
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Via Roma 15, Milano', 'lat' => 45.4642, 'lng' => 9.1900],
                    'code' => 'TCK-DEMO1',
                ],
                [
                    'name' => 'Ratto nel cortile della scuola',
                    'content' => 'Avvistato ratto nel cortile della scuola elementare.',
                    'type_id' => 'animal',
                    'priority_id' => 'high',
                    'status_id' => TicketStatusEnum::IN_REVIEW->value,
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Via delle Rose 42, Torino', 'lat' => 45.0703, 'lng' => 7.6869],
                    'code' => 'TCK-DEMO2',
                ],
                [
                    'name' => 'Richiesta nuovo raccordo stradale',
                    'content' => 'La strada è dissestata, richiesta di rifacimento.',
                    'type_id' => 'road',
                    'priority_id' => 'medium',
                    'status_id' => TicketStatusEnum::IN_PROGRESS->value,
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Strada provinciale 23, Brescia', 'lat' => 45.5414, 'lng' => 10.2298],
                    'code' => 'TCK-DEMO3',
                ],
                [
                    'name' => 'Segnalazione discesa pericolosa',
                    'content' => 'Pedone ferito in discesa pericolosa.',
                    'type_id' => 'pedestrian',
                    'priority_id' => 'high',
                    'status_id' => TicketStatusEnum::RESOLVED->value,
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Via del Monte 10, Napoli', 'lat' => 40.8518, 'lng' => 14.2681],
                    'code' => 'TCK-DEMO4',
                ],
                [
                    'name' => 'Vandalismo nella sala d\'attesa',
                    'content' => 'Venezie sui sedili del bus.',
                    'type_id' => 'vandalism',
                    'priority_id' => 'medium',
                    'status_id' => TicketStatusEnum::CLOSED->value,
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Piazza centrale, Genova', 'lat' => 44.4044, 'lng' => 8.9463],
                    'code' => 'TCK-DEMO5',
                ],
            )
            ->create();

        foreach ($tickets as $ticket) {
            $timestamp = now();
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                'old_status_id' => null,
                'new_status_id' => TicketStatusEnum::PENDING->value,
                'user_id' => null,
                'payload' => ['from' => null, 'to' => TicketStatusEnum::PENDING->value],
                'reason' => 'Segnalazione ricevuta',
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $timestamp,
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'event_type' => TicketActivityEventTypeEnum::Approved->value,
                'old_status_id' => TicketStatusEnum::PENDING->value,
                'new_status_id' => TicketStatusEnum::IN_REVIEW->value,
                'user_id' => null,
                'payload' => [
                    'from' => TicketStatusEnum::PENDING->value,
                    'to' => TicketStatusEnum::IN_REVIEW->value,
                ],
                'reason' => 'Verificato, corrispondente ai problemi segnalati',
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $timestamp->copy()->addHours(2),
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'event_type' => TicketActivityEventTypeEnum::Assignment->value,
                'old_status_id' => TicketStatusEnum::IN_REVIEW->value,
                'new_status_id' => TicketStatusEnum::IN_PROGRESS->value,
                'user_id' => null,
                'payload' => [
                    'from' => TicketStatusEnum::IN_REVIEW->value,
                    'to' => TicketStatusEnum::IN_PROGRESS->value,
                    'to_label' => 'ULSS Brescia',
                ],
                'reason' => 'Assegnato a ULSS Brescia per intervento',
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $timestamp->copy()->addDays(1),
            ]);

            $ticketKey = $ticket->getKey();

            if ($ticket->code === 'TCK-DEMO4') {
                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'event_type' => TicketActivityEventTypeEnum::Closed->value,
                    'old_status_id' => TicketStatusEnum::IN_PROGRESS->value,
                    'new_status_id' => TicketStatusEnum::CLOSED->value,
                    'user_id' => null,
                    'payload' => [
                        'from' => TicketStatusEnum::IN_PROGRESS->value,
                        'to' => TicketStatusEnum::CLOSED->value,
                    ],
                    'reason' => 'Non vogliamo risolvere o non è nostro compito',
                    'visibility' => TicketActivityVisibilityEnum::Public->value,
                    'created_at' => $timestamp->copy()->addDays(5),
                ]);
            }

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'commentable_type' => Ticket::class,
                'commentable_key' => $ticketKey,
                'author_id' => null,
                'comment' => 'Grazie per l\'attenzione, terremo monitorato lo stato.',
                'created_at' => $timestamp->copy()->addHours(1),
            ]);

            if ($ticket->code === 'TCK-DEMO2') {
                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'event_type' => TicketActivityEventTypeEnum::MailSent->value,
                    'old_status_id' => null,
                    'new_status_id' => null,
                    'user_id' => null,
                    'payload' => ['to' => 'ULSS Milano', 'subject' => 'Ratto in cortile'],
                    'reason' => 'Mail inviata a ULSS per intervento urgente',
                    'visibility' => TicketActivityVisibilityEnum::Public->value,
                    'created_at' => $timestamp->copy()->addHours(12),
                ]);
            }
        }
    }
}

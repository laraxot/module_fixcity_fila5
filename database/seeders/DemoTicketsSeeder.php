<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketComment;

class DemoTicketsSeeder extends Seeder
{
    public function run(): void
    {
        // Crea 5 ticket realistici per la demo
        $tickets = TicketFactory::new()
            ->count(5)
            ->sequence(
                [
                    'name' => 'Danneggiamento panchina pubblica',
                    'content' => 'La panchina in via Roma è rotta, segnalazione per riparazione.',
                    'type_id' => 'street_furniture',
                    'priority_id' => 'low',
                    'status_id' => 'pending',
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Via Roma 15, Milano', 'lat' => 45.4642, 'lng' => 9.1900],
                    'code' => 'TCK-DEMO1',
                ],
                [
                    'name' => 'Ratto nel cortile della scuola',
                    'content' => 'Avvistato ratto nel cortile della scuola elementare.',
                    'type_id' => 'animal',
                    'priority_id' => 'high',
                    'status_id' => 'approved',
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Via delle Rose 42, Torino', 'lat' => 45.0703, 'lng' => 7.6869],
                    'code' => 'TCK-DEMO2',
                ],
                [
                    'name' => 'Richiesta nuovo raccordo stradale',
                    'content' => 'La strada è dissestata, richiesta di rifacimento.',
                    'type_id' => 'road',
                    'priority_id' => 'medium',
                    'status_id' => 'assigned',
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Strada provinciale 23, Brescia', 'lat' => 45.5414, 'lng' => 10.2298],
                    'code' => 'TCK-DEMO3',
                ],
                [
                    'name' => 'Segnalazione discesa pericolosa',
                    'content' => 'Pedone ferito in discesa pericolosa.',
                    'type_id' => 'pedestrian',
                    'priority_id' => 'high',
                    'status_id' => 'resolved',
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Via del Monte 10, Napoli', 'lat' => 40.8518, 'lng' => 14.2681],
                    'code' => 'TCK-DEMO4',
                ],
                [
                    'name' => 'Vandalismo nella sala d\'attesa',
                    'content' => 'Venezie sui sedili del bus.',
                    'type_id' => 'vandalism',
                    'priority_id' => 'medium',
                    'status_id' => 'closed',
                    'owner_id' => '01a117c0-b8f0-73bb-8e81-1685acb426dd',
                    'location' => ['address' => 'Piazza centrale, Genova', 'lat' => 44.4044, 'lng' => 8.9463],
                    'code' => 'TCK-DEMO5',
                ],
            )
            ->create();

        // Crea attività di timeline realistiche per ogni ticket (passaggi)
        foreach ($tickets as $ticket) {
            $timestamp = now();
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                'old_status_id' => null,
                'new_status_id' => TicketStatusEnum::Pending->value,
                'user_id' => null,
                'payload' => ['from' => null, 'to' => TicketStatusEnum::Pending->value],
                'reason' => 'Segnalazione ricevuta',
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $timestamp,
            ]);

            // Approvazione del moderatore
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'event_type' => TicketActivityEventTypeEnum::Approved->value,
                'old_status_id' => TicketStatusEnum::Pending->value,
                'new_status_id' => TicketStatusEnum::Approved->value,
                'user_id' => null,
                'payload' => ['from' => TicketStatusEnum::Pending->value, 'to' => TicketStatusEnum::Approved->value],
                'reason' => 'Verificato, corrispondente ai problemi segnalati',
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $timestamp->addHours(2),
            ]);

            // Assegnazione a ULSS/Comune/Vigili
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'event_type' => TicketActivityEventTypeEnum::Assignment->value,
                'old_status_id' => TicketStatusEnum::Approved->value,
                'new_status_id' => TicketStatusEnum::Assigned->value,
                'user_id' => null,
                'payload' => ['from' => TicketStatusEnum::Approved->value, 'to' => TicketStatusEnum::Assigned->value, 'to_label' => 'ULSS Brescia'],
                'reason' => 'Assegnato a ULSS Brescia per intervento',
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $timestamp->addDays(1),
            ]);

            // Simuliamo uno scenario di chiusura con motivo per il ticket 4 (risolto con motivo)
            if ($ticket->id === 4) {
                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'event_type' => TicketActivityEventTypeEnum::Closed->value,
                    'old_status_id' => TicketStatusEnum::Assigned->value,
                    'new_status_id' => TicketStatusEnum::Closed->value,
                    'user_id' => null,
                    'payload' => ['from' => TicketStatusEnum::Assigned->value, 'to' => TicketStatusEnum::Closed->value],
                    'reason' => 'Non vogliamo risolvere o non è nostro compito',
                    'visibility' => TicketActivityVisibilityEnum::Public->value,
                    'created_at' => $timestamp->addDays(5),
                ]);
            }

            // Commenti ai ticket
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'commentable_type' => Ticket::class,
                'commentable_key' => (string) $ticket->getKey(),
                'author_id' => null,
                'comment' => 'Grazie per l\'attenzione, terremo monitorato lo stato.',
                'created_at' => $timestamp->addHours(1),
            ]);

            // Simuliamo il tracciamento mail per il ticket 2 (approvato)
            if ($ticket->id === 2) {
                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'event_type' => TicketActivityEventTypeEnum::MailSent->value,
                    'old_status_id' => null,
                    'new_status_id' => null,
                    'user_id' => null,
                    'payload' => ['to' => 'ULSS Milano', 'subject' => 'Ratto in cortile'],
                    'reason' => 'Mail inviata a ULSS per intervento urgente',
                    'visibility' => TicketActivityVisibilityEnum::Public->value,
                    'created_at' => $timestamp->addHours(12),
                ]);
            }
        }
    }
}

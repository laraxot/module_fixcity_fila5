<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\Category;
use Modules\Xot\Datas\XotData;

/**
 * Città demo multiple — espande il seeder con coordinate reali italiane.
 *
 * Copre: Veneto, Lombardia, Toscana, Lazio, Campania, Piemonte, Emilia-Romagna.
 * Ogni città ha 3-5 ticket demo per dimostrare la mappa multi-regione.
 */
class DemoCitiesSeeder extends Seeder
{
    /**
     * Coordinate reali per demo multi-città italiana.
     *
     * @var list<array{
     *     code: string,
     *     slug: string,
     *     name: string,
     *     content: string,
     *     status: TicketStatusEnum,
     *     priority: TicketPriorityEnum,
     *     type: TicketTypeEnum,
     *     lat: string,
     *     lng: string,
     *     address: string,
     *     city: string,
     *     province: string,
     *     country: string
     * }>
     */
    private const array ITALIAN_CITIES = [
        // Veneto — Mogliano Veneto (coordinated base)
        [
            'code' => 'VEN-001', 'slug' => 'veneto-buca-strada',
            'name' => 'Buca profonda in via Roma',
            'content' => 'Avvallamento sul manto stradale, rischio per veicoli e pedoni.',
            'status' => TicketStatusEnum::IN_PROGRESS, 'priority' => TicketPriorityEnum::HIGH,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            'lat' => '45.562246', 'lng' => '12.249756',
            'address' => 'Via Roma 5, Mogliano Veneto', 'city' => 'Mogliano Veneto', 'province' => 'Treviso', 'country' => 'Italia',
        ],
        // Lombardia — Milano
        [
            'code' => 'LOM-001', 'slug' => 'lom-lampione-spento',
            'name' => 'Lampione spento via Torino',
            'content' => 'Illuminazione pubblica non funzionante da oltre una settimana.',
            'status' => TicketStatusEnum::OPEN, 'priority' => TicketPriorityEnum::MEDIUM,
            'type' => TicketTypeEnum::PUBLIC_LIGHTING,
            'lat' => '45.464203', 'lng' => '9.189577',
            'address' => 'Via Torino 45, Milano', 'city' => 'Milano', 'province' => 'Milano', 'country' => 'Italia',
        ],
        // Toscana — Firenze
        [
            'code' => 'TOS-001', 'slug' => 'tos-cestino-pieno',
            'name' => 'Cestino rifiuti stracolmo piazza Santo Spirito',
            'content' => 'Contenitore per la raccolta differenziata non svuotato da due giorni.',
            'status' => TicketStatusEnum::OPEN, 'priority' => TicketPriorityEnum::MEDIUM,
            'type' => TicketTypeEnum::WASTE_COLLECTION,
            'lat' => '43.769566', 'lng' => '11.255823',
            'address' => 'Piazza Santo Spirito 12, Firenze', 'city' => 'Firenze', 'province' => 'Firenze', 'country' => 'Italia',
        ],
        // Lazio — Roma
        [
            'code' => 'LAZ-001', 'slug' => 'laz-segnaletica-abbattuta',
            'name' => 'Segnaletica stradale abbattuta via del Corso',
            'content' => 'Cartello di segnalazione caduto a terra, rischio incidenti.',
            'status' => TicketStatusEnum::IN_REVIEW, 'priority' => TicketPriorityEnum::URGENT,
            'type' => TicketTypeEnum::PUBLIC_SAFETY,
            'lat' => '41.902783', 'lng' => '12.496366',
            'address' => 'Via del Corso 200, Roma', 'city' => 'Roma', 'province' => 'Roma', 'country' => 'Italia',
        ],
        // Campania — Napoli
        [
            'code' => 'CAM-001', 'slug' => 'cam-rifiuto',
            'name' => 'Rifiuto fognario in via Toledo',
            'content' => 'Fogna a cielo aperto con reflui visibili, odori molesti.',
            'status' => TicketStatusEnum::OPEN, 'priority' => TicketPriorityEnum::URGENT,
            'type' => TicketTypeEnum::SEWAGE_AND_DRAINAGE,
            'lat' => '40.835850', 'lng' => '14.248814',
            'address' => 'Via Toledo 85, Napoli', 'city' => 'Napoli', 'province' => 'Napoli', 'country' => 'Italia',
        ],
        // Piemonte — Torino
        [
            'code' => 'PIE-001', 'slug' => 'pie-allagamento-sottopasso',
            'name' => 'Sottopasso allagato via Garibaldi',
            'content' => 'Impossibile transitare a causa dell\'accumulo d\'acqua dopo il temporale.',
            'status' => TicketStatusEnum::ON_HOLD, 'priority' => TicketPriorityEnum::HIGH,
            'type' => TicketTypeEnum::SEWAGE_AND_DRAINAGE,
            'lat' => '45.070261', 'lng' => '7.686858',
            'address' => 'Via Garibaldi 10, Torino', 'city' => 'Torino', 'province' => 'Torino', 'country' => 'Italia',
        ],
        // Emilia-Romagna — Bologna
        [
            'code' => 'EMI-001', 'slug' => 'emi-erba-alta',
            'name' => 'Erba alta e incuria marciapiedi via San Vitale',
            'content' => 'Vegetazione che ostacola il passaggio dei pedoni.',
            'status' => TicketStatusEnum::OPEN, 'priority' => TicketPriorityEnum::LOW,
            'type' => TicketTypeEnum::PARKS_AND_GARDENS,
            'lat' => '44.494887', 'lng' => '11.342616',
            'address' => 'Via San Vitale 25, Bologna', 'city' => 'Bologna', 'province' => 'Bologna', 'country' => 'Italia',
        ],
    ];

    public function run(): void
    {
        $ownerClass = XotData::make()->getUserClass();
        $ownerIdValue = $ownerClass::query()
            ->where('email', DemoUsersSeeder::CITIZEN_EMAIL)
            ->value('id');

        if (! is_int($ownerIdValue) && ! is_string($ownerIdValue)) {
            if ($this->command !== null) {
                $this->command->warn('DemoCitiesSeeder: cittadino demo assente — saltato.');
            }

            return;
        }

        foreach (self::ITALIAN_CITIES as $record) {
            $this->upsertCityTicket($record, $ownerIdValue);
        }

        if ($this->command !== null) {
            $this->command->info('DemoCitiesSeeder: '.count(self::ITALIAN_CITIES).' ticket multi-città creati.');
        }
    }

    /** @param array{code: string, slug: string, name: string, content: string, status: TicketStatusEnum, priority: TicketPriorityEnum, type: TicketTypeEnum, lat: string, lng: string, address: string, city: string, province: string, country: string} $record */
    private function upsertCityTicket(array $record, int|string $ownerId): void
    {
        $ticket = Ticket::query()->updateOrCreate(
            ['code' => $record['code']],
            [
                'name' => $record['name'],
                'content' => $record['content'],
                'owner_id' => $ownerId,
                'ticket_prefix' => 'DEMO',
            ],
        );

        $ticket->status = $record['status'];
        $ticket->priority = $record['priority'];
        $ticket->type = $record['type'];
        $ticket->location = [
            'lat' => $record['lat'],
            'lng' => $record['lng'],
            'latitude' => $record['lat'],
            'longitude' => $record['lng'],
            'address' => $record['address'],
            'display_name' => $record['address'].', '.$record['city'].', '.$record['province'].', '.$record['country'],
            'provider' => 'seed',
            'city' => $record['city'],
            'province' => $record['province'],
            'country' => $record['country'],
            'country_code' => 'it',
        ];
        $ticket->latitude = $record['lat'];
        $ticket->longitude = $record['lng'];
        $ticket->save();

        Ticket::withoutEvents(static function () use ($ticket, $record): void {
            Ticket::query()->whereKey($ticket->id)->update(['slug' => $record['slug']]);
        });
    }
}

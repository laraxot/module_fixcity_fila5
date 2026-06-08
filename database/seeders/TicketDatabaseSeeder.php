<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Actions\GenerateTicketsJsonAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Illuminate\Support\Facades\File;

/**
 * Ticket dimostrativi per presentazione FO: mappa, elenco, dettaglio /it/tickets/{id}.
 *
 * @see docs/stories/STORY-135-ticket-presentation-seeders.md
 */
class TicketDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = $this->resolveOwnerId();
        if ($ownerId === null) {
            $this->command?->warn('TicketDatabaseSeeder: nessun utente nel DB — seed ticket saltato.');

            return;
        }

        foreach ($this->presentationRecords() as $record) {
            $this->upsertPresentationTicket($record, $ownerId);
        }

        $path = app(GenerateTicketsJsonAction::class)->execute();
        $this->command?->info('TicketDatabaseSeeder: GeoJSON aggiornato in '.$path);
    }

    private function resolveOwnerId(): int|string|null
    {
        /** @var class-string<User> $userModel */
        $userModel = config('auth.providers.users.model', User::class);
        $id = $userModel::query()->orderBy('id')->value('id');

        if (is_int($id) || (is_string($id) && $id !== '')) {
            return $id;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function upsertPresentationTicket(array $record, int|string $ownerId): void
    {
        $lat = (string) $record['lat'];
        $lng = (string) $record['lng'];
        $address = (string) $record['address'];

        $location = [
            'lat' => $lat,
            'lng' => $lng,
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address,
            'display_name' => $address.', Veneto, Italia',
            'provider' => 'seed',
            'city' => (string) ($record['city'] ?? 'Mogliano Veneto'),
            'province' => 'Treviso',
            'country' => 'Italia',
            'country_code' => 'it',
        ];

        $demoCode = (string) ($record['code'] ?? '');
        $desiredSlug = (string) $record['slug'];

        /** @var Ticket $ticket */
        $ticket = Ticket::query()->updateOrCreate(
            ['code' => $demoCode],
            [
                'name' => (string) $record['name'],
                'content' => (string) $record['content'],
                'owner_id' => $ownerId,
                'ticket_prefix' => 'DEMO',
            ],
        );

        $ticket->status = $record['status'];
        $ticket->priority = $record['priority'];
        $ticket->type = $record['type'];
        $ticket->location = $location;
        $ticket->latitude = $lat;
        $ticket->longitude = $lng;
        $ticket->save();

        // Aggiunta immagine placeholder se non ne ha già
        if ($ticket->getMedia('attachments')->isEmpty()) {
            $placeholder = base_path('Themes/Sixteen/Main_files/five/assets/images/image-disservizio.png');
            if (File::exists($placeholder)) {
                $ticket->addMedia($placeholder)
                    ->preservingOriginal()
                    ->toMediaCollection('attachments');
            }
        }

        // HasSlug rigenera da name — ripristiniamo slug stabile per idempotenza seed.
        Ticket::withoutEvents(static function () use ($ticket, $desiredSlug): void {
            Ticket::query()->whereKey($ticket->id)->update(['slug' => $desiredSlug]);
        });
    }

    /**
     * Coordinate nell'area Mogliano Veneto / Treviso — visibili in mappa e FO.
     *
     * @return list<array<string, mixed>>
     */
    private function presentationRecords(): array
    {
        return [
            [
                'code' => 'DEMO-001',
                'slug' => 'demo-buca-via-morandi',
                'name' => 'Buca profonda in via Morandi',
                'content' => 'Avvallamento sul manto stradale davanti al civico 5, rischio per due ruote e pedoni.',
                'status' => TicketStatusEnum::IN_PROGRESS,
                'priority' => TicketPriorityEnum::HIGH,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'lat' => '45.562246',
                'lng' => '12.249756',
                'address' => 'Via Rodolfo Morandi 5, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-002',
                'slug' => 'demo-lampione-piazza-marconi',
                'name' => 'Lampione spento piazza Marconi',
                'content' => 'Illuminazione pubblica non funzionante da oltre una settimana; area poco frequentata di sera.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::PUBLIC_LIGHTING,
                'lat' => '45.555890',
                'lng' => '12.242310',
                'address' => 'Piazza Guglielmo Marconi, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-003',
                'slug' => 'demo-cestino-via-roma',
                'name' => 'Cestino rifiuti stracolmo via Roma',
                'content' => 'Contenitore per la raccolta differenziata non svuotato; odori e sversamenti sul marciapiede.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::WASTE_COLLECTION,
                'lat' => '45.558120',
                'lng' => '12.255400',
                'address' => 'Via Roma, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-004',
                'slug' => 'demo-panchina-parco-belvedere',
                'name' => 'Panchina danneggiata parco Belvedere',
                'content' => 'Seduta instabile e lamiera arrugginita; area giochi adiacente frequentata da famiglie.',
                'status' => TicketStatusEnum::RESOLVED,
                'priority' => TicketPriorityEnum::LOW,
                'type' => TicketTypeEnum::URBAN_FURNITURE,
                'lat' => '45.551700',
                'lng' => '12.261200',
                'address' => 'Parco Belvedere, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-005',
                'slug' => 'demo-albero-via-piovan',
                'name' => 'Ramo pericoloso su via Piovan',
                'content' => 'Albero con ramo spezzato a bassa altezza; intervento potatura urgente.',
                'status' => TicketStatusEnum::IN_PROGRESS,
                'priority' => TicketPriorityEnum::HIGH,
                'type' => TicketTypeEnum::PARKS_AND_GARDENS,
                'lat' => '45.564100',
                'lng' => '12.238900',
                'address' => 'Via Piovan, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-006',
                'slug' => 'demo-tombino-via-bachelet',
                'name' => 'Tombino intasato via Bachelet',
                'content' => 'Acqua stagnante dopo pioggia; tombino di scolo ostruito da detriti.',
                'status' => TicketStatusEnum::ON_HOLD,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::SEWAGE_AND_DRAINAGE,
                'lat' => '45.549800',
                'lng' => '12.247500',
                'address' => 'Via Bachelet, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-007',
                'slug' => 'demo-scuola-via-don-bosco',
                'name' => 'Infiltrazione tetto scuola elementare',
                'content' => 'Macchie di umidità nell\'atrio della scuola; segnalato dal personale scolastico.',
                'status' => TicketStatusEnum::IN_REVIEW,
                'priority' => TicketPriorityEnum::HIGH,
                'type' => TicketTypeEnum::PUBLIC_BUILDINGS,
                'lat' => '45.557400',
                'lng' => '12.245200',
                'address' => 'Via Don Bosco, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-008',
                'slug' => 'demo-pensilina-via-venezia',
                'name' => 'Vetro rotto pensilina bus via Venezia',
                'content' => 'Vandalismo alla fermata del bus; frammenti di vetro a terra pericolosi.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::PUBLIC_TRANSPORT,
                'lat' => '45.553200',
                'lng' => '12.249100',
                'address' => 'Via Venezia, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-009',
                'slug' => 'demo-barriera-via-terraglio',
                'name' => 'Barriera stradale divelta via Terraglio',
                'content' => 'Guardrail danneggiato a seguito di incidente; protezione laterale mancante.',
                'status' => TicketStatusEnum::IN_PROGRESS,
                'priority' => TicketPriorityEnum::HIGH,
                'type' => TicketTypeEnum::PUBLIC_SAFETY,
                'lat' => '45.568900',
                'lng' => '12.235600',
                'address' => 'Via Terraglio, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-010',
                'slug' => 'demo-rifiuti-via-maroncelli',
                'name' => 'Abbandono illecito rifiuti via Maroncelli',
                'content' => 'Elettrodomestici e mobili vecchi abbandonati sul ciglio della strada.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::ENVIRONMENTAL_REPORTS,
                'lat' => '45.559800',
                'lng' => '12.263400',
                'address' => 'Via Maroncelli, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-011',
                'slug' => 'demo-segnaletica-piazza-piero-della-francesca',
                'name' => 'Segnale di STOP abbattuto',
                'content' => 'Il cartello di STOP all\'incrocio è a terra, rischio incidenti.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::URGENT,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'lat' => '45.560100',
                'lng' => '12.241000',
                'address' => 'Piazza Piero della Francesca, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-012',
                'slug' => 'demo-erba-alta-via-garibaldi',
                'name' => 'Erba alta e incuria marciapiedi',
                'content' => 'La vegetazione ostacola il passaggio dei pedoni in via Garibaldi.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::LOW,
                'type' => TicketTypeEnum::PARKS_AND_GARDENS,
                'lat' => '45.554500',
                'lng' => '12.251200',
                'address' => 'Via Giuseppe Garibaldi, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-013',
                'slug' => 'demo-fontanella-piazza-caduti',
                'name' => 'Fontanella pubblica perde acqua',
                'content' => 'Spreco d\'acqua continuo dalla fontana storica in piazza Caduti.',
                'status' => TicketStatusEnum::IN_PROGRESS,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::URBAN_FURNITURE,
                'lat' => '45.556700',
                'lng' => '12.243500',
                'address' => 'Piazza Caduti, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-014',
                'slug' => 'demo-illuminazione-via-mazzini',
                'name' => 'Intera via al buio',
                'content' => 'Tutti i lampioni di via Mazzini sono spenti da due sere.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::HIGH,
                'type' => TicketTypeEnum::PUBLIC_LIGHTING,
                'lat' => '45.558900',
                'lng' => '12.246700',
                'address' => 'Via Giuseppe Mazzini, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-015',
                'slug' => 'demo-graffiti-biblioteca',
                'name' => 'Vandalismo pareti biblioteca',
                'content' => 'Nuovi graffiti apparsi sulla facciata esterna della biblioteca comunale.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::LOW,
                'type' => TicketTypeEnum::PUBLIC_BUILDINGS,
                'lat' => '45.557100',
                'lng' => '12.248200',
                'address' => 'Via Cesare Battisti, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-016',
                'slug' => 'demo-rumore-cantiere-notturno',
                'name' => 'Eccessivo rumore notturno cantiere',
                'content' => 'Lavori in corso oltre l\'orario consentito, disturbo della quiete pubblica.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::ENVIRONMENTAL_REPORTS,
                'lat' => '45.552100',
                'lng' => '12.258900',
                'address' => 'Via XXIV Maggio, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-017',
                'slug' => 'demo-marciapiede-via-trieste',
                'name' => 'Lastre marciapiede sconnesse',
                'content' => 'Pericolo inciampo in via Trieste a causa delle radici degli alberi che sollevano il marciapiede.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::MEDIUM,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'lat' => '45.559200',
                'lng' => '12.252300',
                'address' => 'Via Trieste, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-018',
                'slug' => 'demo-segnaletica-bus-via-ronzinella',
                'name' => 'Tabella orari bus illeggibile',
                'content' => 'Orari coperti da adesivi e sporcizia in via Ronzinella.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::LOW,
                'type' => TicketTypeEnum::PUBLIC_TRANSPORT,
                'lat' => '45.550500',
                'lng' => '12.240100',
                'address' => 'Via Ronzinella, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-019',
                'slug' => 'demo-specchio-incrocio-via-zermanese',
                'name' => 'Specchio parabolico rotto',
                'content' => 'Incrocio cieco pericoloso a causa dello specchio infranto.',
                'status' => TicketStatusEnum::IN_PROGRESS,
                'priority' => TicketPriorityEnum::HIGH,
                'type' => TicketTypeEnum::PUBLIC_SAFETY,
                'lat' => '45.565600',
                'lng' => '12.257800',
                'address' => 'Via Zermanese, Mogliano Veneto',
            ],
            [
                'code' => 'DEMO-020',
                'slug' => 'demo-allagamento-sottopasso',
                'name' => 'Sottopasso allagato via Olme',
                'content' => 'Impossibile transitare a causa dell\'accumulo d\'acqua dopo il temporale.',
                'status' => TicketStatusEnum::OPEN,
                'priority' => TicketPriorityEnum::URGENT,
                'type' => TicketTypeEnum::SEWAGE_AND_DRAINAGE,
                'lat' => '45.548200',
                'lng' => '12.253400',
                'address' => 'Via Olme, Mogliano Veneto',
            ],
        ];
    }
}

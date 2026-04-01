<?php

/**
 * mettere getDescription.
 */

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/*
Tipologie di Riparazioni Segnalabili
------
Manutenzione Stradale

Descrizione: Segnalazioni relative a buche, crepe, segnaletica stradale danneggiata, ecc.
Esempio: Buche sull'asfalto, segnaletica stradale non visibile.
Colore: #ff9800 (Orange)
Icona: 🛤️ (Heroicons: Road)
------
Illuminazione Pubblica

Descrizione: Segnalazioni riguardanti lampioni non funzionanti o danneggiati.
Esempio: Lampione spento in una via pubblica.
Colore: #fbc02d (Yellow)
Icona: 💡 (Heroicons: Light Bulb)
------
Raccolta Rifiuti

Descrizione: Problemi con la raccolta dei rifiuti, cestini pieni, rifiuti abbandonati.
Esempio: Cassonetti non svuotati regolarmente.
Colore: #4caf50 (Green)
Icona: 🗑️ (Heroicons: Trash Can)
------
Aree Verdi e Parchi

Descrizione: Manutenzione di parchi, giardini, e aree verdi, alberi caduti, erba alta.
Esempio: Piante secche, giochi per bambini danneggiati.
Colore: #8bc34a (Light Green)
Icona: 🌳 (Heroicons: Tree)
------
Fognature e Drenaggi

Descrizione: Problemi con fognature o drenaggi bloccati, perdite d'acqua.
Esempio: Tombini intasati, allagamenti.
Colore: #2196f3 (Blue)
Icona: 🚰 (Heroicons: Water Drop)
------
Edifici Pubblici

Descrizione: Riparazioni necessarie in edifici pubblici come scuole, biblioteche, municipi.
Esempio: Finestre rotte, porte danneggiate.
Colore: #3f51b5 (Indigo)
Icona: 🏢 (Heroicons: Office Building)
------
Segnalazioni Ambientali

Descrizione: Problemi di inquinamento, smaltimento illecito di rifiuti, rumore.
Esempio: Discariche abusive, inquinamento acustico.
Colore: #f44336 (Red)
Icona: 🌍 (Heroicons: Earth)

------
Trasporti Pubblici

Descrizione: Problemi legati ai servizi di trasporto pubblico, fermate danneggiate.
Esempio: Pensiline danneggiate, orari non rispettati.
Colore: #9c27b0 (Purple)
Icona: 🚍 (Heroicons: Bus)
------
Arredo Urbano

Descrizione: Manutenzione di panchine, cestini, fontane e altre infrastrutture urbane.
Esempio: Panchine rotte, fontane non funzionanti.
Colore: #00bcd4 (Cyan)
Icona: 🪑 (Heroicons: Chair)
------
Sicurezza Pubblica

Descrizione: Problemi che riguardano la sicurezza dei cittadini, barriere di sicurezza danneggiate.
Esempio: Barriere di sicurezza rotte, illuminazione scarsa in zone pericolose.
Colore: #ff5722 (Deep Orange)
Icona: 🛡️ (Heroicons: Shield)
*/

enum TicketTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    case ROAD_MAINTENANCE = 'road_maintenance';
    case PUBLIC_LIGHTING = 'public_lighting';
    case WASTE_COLLECTION = 'waste_collection';
    case PARKS_AND_GARDENS = 'parks_and_gardens';
    case SEWAGE_AND_DRAINAGE = 'sewage_and_drainage';
    case PUBLIC_BUILDINGS = 'public_buildings';
    case ENVIRONMENTAL_REPORTS = 'environmental_reports';
    case PUBLIC_TRANSPORT = 'public_transport';
    case URBAN_FURNITURE = 'urban_furniture';
    case PUBLIC_SAFETY = 'public_safety';
    case COMPLAINT = 'complaint';
    case SUGGESTION = 'suggestion';
    case REPORT = 'report';
    case REQUEST = 'request';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ROAD_MAINTENANCE => 'Manutenzione Stradale',
            self::PUBLIC_LIGHTING => 'Illuminazione Pubblica',
            self::WASTE_COLLECTION => 'Raccolta Rifiuti',
            self::PARKS_AND_GARDENS => 'Aree Verdi e Parchi',
            self::SEWAGE_AND_DRAINAGE => 'Fognature e Drenaggi',
            self::PUBLIC_BUILDINGS => 'Edifici Pubblici',
            self::ENVIRONMENTAL_REPORTS => 'Segnalazioni Ambientali',
            self::PUBLIC_TRANSPORT => 'Trasporti Pubblici',
            self::URBAN_FURNITURE => 'Arredo Urbano',
            self::PUBLIC_SAFETY => 'Sicurezza Pubblica',
            self::COMPLAINT => 'Complaint',
            self::SUGGESTION => 'Suggestion',
            self::REPORT => 'Report',
            self::REQUEST => 'Request',
            self::OTHER => 'Other',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ROAD_MAINTENANCE => '#ff9800',
            self::PUBLIC_LIGHTING => '#fbc02d',
            // 50 => '254, 242, 242',
            // self::PUBLIC_LIGHTING => 'green',
            self::WASTE_COLLECTION => '#4caf50',
            self::PARKS_AND_GARDENS => '#8bc34a',
            self::SEWAGE_AND_DRAINAGE => '#2196f3',
            self::PUBLIC_BUILDINGS => '#3f51b5',
            self::ENVIRONMENTAL_REPORTS => '#f44336',
            self::PUBLIC_TRANSPORT => '#9c27b0',
            self::URBAN_FURNITURE => '#00bcd4',
            self::PUBLIC_SAFETY => '#ff5722',
            self::COMPLAINT => 'danger',
            self::SUGGESTION => 'success',
            self::REPORT => 'warning',
            self::REQUEST => 'info',
            self::OTHER => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ROAD_MAINTENANCE => 'heroicon-o-wrench',
            self::PUBLIC_LIGHTING => 'heroicon-o-light-bulb',
            self::WASTE_COLLECTION => 'heroicon-o-trash',
            self::PARKS_AND_GARDENS => 'heroicon-o-sparkles',
            self::SEWAGE_AND_DRAINAGE => 'heroicon-o-archive-box',
            self::PUBLIC_BUILDINGS => 'heroicon-o-building-office',
            self::ENVIRONMENTAL_REPORTS => 'heroicon-o-globe-alt',
            self::PUBLIC_TRANSPORT => 'fas-bus',
            self::URBAN_FURNITURE => 'fas-couch',
            self::PUBLIC_SAFETY => 'heroicon-o-shield-check',
            self::COMPLAINT => 'heroicon-o-exclamation-triangle',
            self::SUGGESTION => 'heroicon-o-light-bulb',
            self::REPORT => 'heroicon-o-document-report',
            self::REQUEST => 'heroicon-o-document',
            self::OTHER => 'heroicon-o-question-mark-circle',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ROAD_MAINTENANCE => trans('fixcity::ticket.fields.type.options.road_maintenance'),
            self::PUBLIC_LIGHTING => trans('fixcity::ticket.fields.type.options.public_lighting'),
            self::WASTE_COLLECTION => trans('fixcity::ticket.fields.type.options.waste_collection'),
            self::PARKS_AND_GARDENS => trans('fixcity::ticket.fields.type.options.parks_and_gardens'),
            self::SEWAGE_AND_DRAINAGE => trans('fixcity::ticket.fields.type.options.sewage_and_drainage'),
            self::PUBLIC_BUILDINGS => trans('fixcity::ticket.fields.type.options.public_buildings'),
            self::ENVIRONMENTAL_REPORTS => trans('fixcity::ticket.fields.type.options.environmental_reports'),
            self::PUBLIC_TRANSPORT => trans('fixcity::ticket.fields.type.options.public_transport'),
            self::URBAN_FURNITURE => trans('fixcity::ticket.fields.type.options.urban_furniture'),
            self::PUBLIC_SAFETY => trans('fixcity::ticket.fields.type.options.public_safety'),
            self::COMPLAINT => trans('fixcity::ticket.fields.type.options.complaint'),
            self::SUGGESTION => trans('fixcity::ticket.fields.type.options.suggestion'),
            self::REPORT => trans('fixcity::ticket.fields.type.options.report'),
            self::REQUEST => trans('fixcity::ticket.fields.type.options.request'),
            self::OTHER => trans('fixcity::ticket.fields.type.options.other'),
        };
    }

    public static function default(): static
    {
        return self::OTHER;
    }
}

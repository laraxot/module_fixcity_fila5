<?php

/**
 * mettere getDescription.
 */

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

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
    use EnumTrait;
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

    public static function default(): static
    {
        return self::OTHER;
    }
}

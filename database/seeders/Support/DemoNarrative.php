<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;

/**
 * Testi italiani della demo: commenti di cittadini e di operatori, motivazioni delle
 * attivita' e note delle ore lavorate. I testi sono accorciati di proposito: la
 * casella di riepilogo ticket deve restare leggibile anche con 500 segnalazioni.
 */
final class DemoNarrative
{
    /**
     * Commenti dell'ente, per stato raggiunto.
     *
     * @var array<string, non-empty-list<non-empty-string>>
     */
    private const array STAFF_BY_STATUS = [
        'pending' => [
            'Segnalazione ricevuta e assegnata al nostro ufficio. Provvediamo a verificare la posizione.',
            'La pratica e\' in carico: nei prossimi giorni un tecnico passera\' per il sopralluogo.',
        ],
        'in_review' => [
            'Sopralluogo eseguito. Il quadro e\' chiaro, procediamo a programmare l\'intervento.',
            'La verifica sul campo conferma quanto segnalato. Occorrono materiali e mezzo operativo.',
        ],
        'in_progress' => [
            'Intervento in corso: la squadra e\' al lavoro in questa via.',
            'Siamo in fase di esecuzione. La segnalazione si aggiorna a lavoro concluso.',
            'Lavori iniziati, al momento si tratta di messa in sicurezza. Poi passiamo al ripristino definitivo.',
        ],
        'on_hold' => [
            'Intervento sospeso: l\'esecuzione e\' subordinata all\'approvazione del finanziamento.',
            'Sospesi i lavori fino al via libera della ditta incaricata. Vi teniamo aggiornati.',
        ],
        'resolved' => [
            'Intervento concluso. La situazione segnalata e\' stata risolta.',
            'Lavori terminati e verificati. Grazie a chi ci ha segnalato, e\' stato possibile intervenire.',
        ],
        'closed' => [
            'Pratica chiusa. Per qualsiasi ulteriore evenienza restiamo a disposizione.',
        ],
        'reopened' => [
            'La pratica e\' stata riaperta: il problema si e\' ripresentato, torniamo ad occuparcene.',
        ],
    ];

    /**
     * Commenti del cittadino, per stato raggiunto.
     *
     * @var array<string, non-empty-list<non-empty-string>>
     */
    private const array CITIZEN_BY_STATUS = [
        'open' => [
            'Ho segnalato ieri sera, l\'indirizzo e\' corretto. Resto in attesa di un riscontro.',
            'Aggiungo che il problema si verifica tutti i giorni, non e\' un episodio isolato.',
        ],
        'pending' => [
            'Grazie per la presa in carico. Spero che si risolva presto, e\' un punto molto frequentato.',
        ],
        'in_review' => [
            'Ho visto che avete fatto il sopralluogo. Il tecnico ha guardato anche il marciapiede?',
            'Grazie, e\' giusto cosi\' fate attenzione, la zona di sera e\' pericolosa.',
        ],
        'in_progress' => [
            'Grazie per l\'aggiornamento. Quando finite, segnatelo cosi\' lo segnalo ai vicini.',
            'Finalmente si muove qualcosa. Se serve la mia collaborazione per le prove, sono disponibile.',
        ],
        'on_hold' => [
            'Mi dispiace sentire che si ferma tutto. Perche\' serve tanto tempo?',
            'Va bene, ma spero non si tratti di mesi: il disagio per noi residenti e\' quotidiano.',
        ],
        'resolved' => [
            'Confermo che il problema e\' risolto, complimenti per la tempestivita\'.',
            'Passando oggi ho visto i lavori, e\' tutto a posto. Grazie.',
        ],
        'closed' => [
            'Pratica chiusa, per me e\' risolto. Buon lavoro.',
        ],
        'reopened' => [
            'Purtroppo il problema si e\' ripresentato dopo poco. Riapro la segnalazione.',
        ],
    ];

    /**
     * Motivazioni delle attivita' di stato.
     *
     * @var array<string, non-empty-list<non-empty-string>>
     */
    private const array ACTIVITY_REASONS = [
        'pending' => [
            'Segnalazione presa in carico dall\'ufficio competente.',
            'Assegnazione interna alla squadra di intervento.',
        ],
        'in_review' => [
            'Sopralluogo completato, pratica in verifica tecnica.',
            'Verifica sul campo in corso di svolgimento.',
        ],
        'in_progress' => [
            'Inizio dei lavori in cantiere.',
            'Intervento esecutivo avviato dalla squadra.',
        ],
        'on_hold' => [
            'Lavori sospesi in attesa di approvazione.',
            'Sospensione per indisponibilita\' del mezzo operativo.',
        ],
        'resolved' => [
            'Intervento eseguito e verificato dall\'ufficio tecnico.',
            'Lavori conclusi, la segnalazione e\' risolta.',
        ],
        'closed' => [
            'Chiusura della pratica, nessuna ulteriore azione necessaria.',
        ],
        'reopened' => [
            'Riapertura su richiesta del cittadino segnalante.',
        ],
    ];

    /**
     * Note delle ore lavorate, per tipologia di attivita'.
     *
     * @var list<non-empty-string>
     */
    private const array HOUR_NOTES = [
        'Sopralluogo e rilievo fotografico del punto',
        'Rimozione del materiale degradato e pulizia dell\'area',
        'Ripristino del manto stradale conBinder',
        'Sostituzione del punto luce e cablaggio',
        'Verifica tecnica e collaudo dell\'intervento',
        'Scarico e trasporto del materiale di risulta',
        'Raccolta differenziata straordinaria nel punto',
        'Potatura e messa in sicurezza della vegetazione',
        'Ripristino della segnaletica verticale e orizzontale',
        'Manutenzione ordinaria programmata',
    ];

    /**
     * Tipologie di attivita' (categoria `activities`), valorizzate dal TicketHourSeeder.
     *
     * @var list<array{name: non-empty-string, description: non-empty-string}>
     */
    public const array ACTIVITY_TYPES = [
        ['name' => 'Sopralluogo', 'description' => 'Rilievo sul campo e verifica della segnalazione'],
        ['name' => 'Intervento', 'description' => 'Esecuzione dei lavori di ripristino'],
        ['name' => 'Manutenzione', 'description' => 'Manutenzione ordinaria o programmata'],
        ['name' => 'Verifica', 'description' => 'Collaudo e verifica del risultato'],
        ['name' => 'Logistica', 'description' => 'Trasporto materiali e gestione del cantiere'],
    ];

    public static function staffComment(Generator $faker, TicketStatusEnum $status): string
    {
        return $faker->randomElement(self::STAFF_BY_STATUS[$status->value] ?? self::STAFF_BY_STATUS['pending']);
    }

    public static function citizenComment(Generator $faker, TicketStatusEnum $status): string
    {
        return $faker->randomElement(self::CITIZEN_BY_STATUS[$status->value] ?? self::CITIZEN_BY_STATUS['open']);
    }

    public static function activityReason(Generator $faker, TicketStatusEnum $status): string
    {
        return $faker->randomElement(self::ACTIVITY_REASONS[$status->value] ?? self::ACTIVITY_REASONS['pending']);
    }

    public static function hourNote(Generator $faker): string
    {
        return $faker->randomElement(self::HOUR_NOTES);
    }

    /**
     * Priorita' spiegata al cittadino: rende leggibile il senso dello SLA in demo.
     */
    public static function priorityRationale(Generator $faker, TicketPriorityEnum $priority): string
    {
        return match ($priority->value) {
            'urgent' => 'Rischio immediato per la sicurezza delle persone, servono interventi in giornata.',
            'critical' => 'Condizioni che compromettono la sicurezza o la fruibilita\' dello spazio pubblico.',
            'high' => 'Disfunzione con ricadute su un tratto ampio di utenti o su un servizio essenziale.',
            'medium' => 'Manutenzione programmabile che impatta su un numero limitato di cittadini.',
            default => $faker->randomElement([
                'Miglioria del decoro urbano, non condiziona la sicurezza.',
                'Segnalazione di miglioramento, puo\' attendere la programmazione ordinaria.',
            ]),
        };
    }

    /**
     * Fronte del ticket: cosa il cittadino scrive, in prima persona. Il problema
     * descritto resta la frase principale, la strada arriva come riferimento.
     */
    public static function openingReport(Generator $faker, string $street, string $issue): string
    {
        return sprintf(
            'Buongiorno, segnalo quanto segue dalla segnalazione cittadina. %s Il punto interessato e\' in %s e si verifica %s. Resto a disposizione per qualsiasi verifica sul posto.',
            $issue,
            $street,
            $faker->randomElement([
                'tutti i giorni, soprattutto nelle ore di punta',
                'da diverse settimane, ma e\' peggiorato negli ultimi giorni',
                'dopo ogni pioggia',
                'in qualsiasi momento della giornata',
                'soprattutto al mattino presto e di sera',
            ]),
        );
    }
}

<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Modules\Fixcity\Enums\TicketTypeEnum;

/**
 * Catalogo categorie per la demo investitori.
 *
 * Ogni categoria dichiara la `type` (TicketTypeEnum) con cui viene etichettato il
 * ticket: lo schema `tickets` non ha una colonna `category_id`, quindi il legame
 * categoria -> segnalazione passa dal campo `type`, che e' la tassonomia usata dal
 * backoffice e dal GeoJSON. `weight` regola la frequenza con cui la categoria
 * viene estratta (le buche e l'illuminazione sono molto piu' frequenti del resto).
 *
 * @phpstan-type CategoryRecord array{
 *     id: non-empty-string,
 *     name: non-empty-string,
 *     description: non-empty-string,
 *     icon: non-empty-string,
 *     parent_id: non-empty-string|null,
 *     sort_order: int,
 *     type: TicketTypeEnum,
 *     weight: int,
 *     department: non-empty-string,
 *     titles: non-empty-list<non-empty-string>,
 *     details: non-empty-list<non-empty-string>
 * }
 */
final class DemoCategoryCatalog
{
    /**
     * Uffici/servizi comunali che prendono in carico le segnalazioni.
     *
     * @var list<non-empty-string>
     */
    public const array DEPARTMENTS = [
        'Servizio Manutenzione Strade',
        'Servizio Illuminazione Pubblica',
        'Servizio Igiene Ambientale',
        'Servizio Verde Pubblico',
        'Servizio Mobilita e Trasporti',
        'Servizio Edilizia Scolastica',
        'Servizio Sicurezza Urbana',
        'Servizio Idraulica e Fognature',
        'Servizio Arredo Urbano',
        'Servizio Segnaletica',
    ];

    /**
     * @return list<array{
     *     id: non-empty-string,
     *     name: non-empty-string,
     *     description: non-empty-string,
     *     icon: non-empty-string,
     *     parent_id: non-empty-string|null,
     *     sort_order: int,
     *     type: TicketTypeEnum,
     *     weight: int,
     *     department: non-empty-string,
     *     titles: non-empty-list<non-empty-string>,
     *     details: non-empty-list<non-empty-string>
     * }>
     */
    public static function all(): array
    {
        return array_merge(self::rootCategories(), self::childCategories());
    }

    /**
     * @return list<array{id: non-empty-string, name: non-empty-string, description: non-empty-string, icon: non-empty-string, parent_id: null, sort_order: int, type: TicketTypeEnum, weight: int, department: non-empty-string, titles: non-empty-list<non-empty-string>, details: non-empty-list<non-empty-string>}>
     */
    private static function rootCategories(): array
    {
        return [
            [
                'id' => 'strade',
                'name' => 'Strade e Marciapiedi',
                'description' => 'Buche, crepe, asfalti consumati, marciapiedi e gradini pericolosi',
                'icon' => 'road',
                'parent_id' => null,
                'sort_order' => 1,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'weight' => 22,
                'department' => 'Servizio Manutenzione Strade',
                'titles' => [
                    'Buca profonda su {street}',
                    'Asfalto sfondato all incrocio di {street}',
                    'Marciapiede divelto in {street}',
                    'Crepa longitudinale nel manto di {street}',
                    'Pavimentazione sconnessa in {street}',
                    'Gradino sporgente sul marciapiede di {street}',
                ],
                'details' => [
                    'Avvallamento di circa 8 cm sul manto stradale, le auto e i pedoni faticano a transitare. Rischio concreto per ciclisti e motocicli.',
                    'Il cedimento si e\' aperto dopo le ultime piogge. Il fondo e\' visibile e le ruote ci finiscono dentro.',
                    'Il marciapiede e\' rialzato di oltre 3 cm, pericoloso per carrozzine e passeggini. Segnalo anche la parte di fronte all\'edicola.',
                    'Crepa lunga piu\' di due metri in mezzo alla carreggiata, si allarga giorno dopo giorno. Segnalo perche\' ormai si vede l\'asfalto sotto.',
                ],
            ],
            [
                'id' => 'illuminazione',
                'name' => 'Illuminazione Pubblica',
                'description' => 'Lampioni spenti, pali danneggiati, aree poco illuminate di notte',
                'icon' => 'lightbulb',
                'parent_id' => null,
                'sort_order' => 2,
                'type' => TicketTypeEnum::PUBLIC_LIGHTING,
                'weight' => 18,
                'department' => 'Servizio Illuminazione Pubblica',
                'titles' => [
                    'Lampione spento in {street}',
                    'Palo dell illuminazione abbattuto su {street}',
                    'Tratto di {street} non illuminato',
                    'Lampione che lampeggia in {street}',
                    'Apparecchio Led difettoso su {street}',
                ],
                'details' => [
                    'Il punto luce e\' spento da almeno una settimana, di sera il tratto e\' completamente buio e chi cammina sul marciapiede rischia di finire nel fossato.',
                    'Il palo e\' inclinato in modo pericoloso dopo l\'urto di un mezzo. Serve un intervento urgente perche\' potrebbe cadere.',
                    'Lampione che lampeggia tutta la notte, disturba chi abita al piano superiore. Segnalo anche la spesa energetica inutile.',
                    'Da tre notti la zona e\' buia: e\' il punto di passaggio verso la scuola media, quindi di sera e\' pieno di studenti.',
                ],
            ],
            [
                'id' => 'rifiuti',
                'name' => 'Rifiuti e Pulizia',
                'description' => 'Cassonetti pieni, rifiuti abbandonati, mancata raccolta differenziata',
                'icon' => 'trash',
                'parent_id' => null,
                'sort_order' => 3,
                'type' => TicketTypeEnum::WASTE_COLLECTION,
                'weight' => 16,
                'department' => 'Servizio Igiene Ambientale',
                'titles' => [
                    'Cassonetti stracolmi in {street}',
                    'Rifiuti abbandonati sul lato di {street}',
                    'Mancata raccolta differenziata in {street}',
                    'Sversamento di rifiuti in {street}',
                    'Cestini pubblici colmi in {street}',
                ],
                'details' => [
                    'I contenitori non vengono svuotati da quattro giorni, i sacchi sono accatastati per terra e l\'odore e\' insostenibile.',
                    'C\'e\' una pila di rifiuti abbandonati sul lato della strada, sembra un discarica abusiva. Rifiuti ingombranti e mobili.',
                    'La raccolta differenziata e\' saltata per due settimane, ormai tutto e\' mischiato e non e\' piu\' recuperabile.',
                    'Sacchi neri aperti lungo il muro, con sversamento di liquami. Rischio igienico per la zona.',
                ],
            ],
            [
                'id' => 'verde',
                'name' => 'Verde Pubblico',
                'description' => 'Alberi pericolanti, aree verdi in degrado, attrezzature da gioco',
                'icon' => 'tree',
                'parent_id' => null,
                'sort_order' => 4,
                'type' => TicketTypeEnum::PARKS_AND_GARDENS,
                'weight' => 11,
                'department' => 'Servizio Verde Pubblico',
                'titles' => [
                    'Albero pericolante in {street}',
                    'Area verde abbandonata in {street}',
                    'Attrezzatura da gioco rotta in {street}',
                    'Siepe che ostruisce il marciapiede di {street}',
                    'Erba alta e cigolie in {street}',
                ],
                'details' => [
                    'Il tronco ha una crepa profonda e some rami pendono. In caso di vento il ramo puo\' cadere sui passanti.',
                    'L\'aiuola e\' invasa da sterpi e rifiuti, non e\' piu\' fruibile da famiglie e anziani.',
                    'Lo scivolo e\' staccato dalla base e pendente, i bambini lo usano lo stesso: serve un intervento immediato.',
                    'La siepe invade il marciapiede e costringe i pedoni a scendere in mezzo alla strada.',
                ],
            ],
            [
                'id' => 'segnaletica',
                'name' => 'Segnaletica e Semafori',
                'description' => 'Cartelli abbattuti, segnali illeggibili, semafori guasti',
                'icon' => 'sign',
                'parent_id' => null,
                'sort_order' => 5,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'weight' => 9,
                'department' => 'Servizio Segnaletica',
                'titles' => [
                    'Cartello di STOP abbattuto in {street}',
                    'Semaforo non funzionante in {street}',
                    'Segnale stradale illeggibile in {street}',
                    'Palina del bus divelta in {street}',
                    'Strisce pedonali cancellate in {street}',
                ],
                'details' => [
                    'Il cartello di STOP e\' a terra dopo un incidente, pericolosissimo per chi arriva senza fermarsi.',
                    'Il semaforo resta sempre rosso, la coda di auto si forma fino all\'incrocio successivo anche nelle ore di punta.',
                    'La segnaletica orizzontale e\' completamente cancellata, i pedoni non capiscono dove attraversare.',
                ],
            ],
            [
                'id' => 'arredo',
                'name' => 'Arredo Urbano',
                'description' => 'Panchine, fontane, monumento, manufatti cittadini danneggiati',
                'icon' => 'bench',
                'parent_id' => null,
                'sort_order' => 6,
                'type' => TicketTypeEnum::URBAN_FURNITURE,
                'weight' => 8,
                'department' => 'Servizio Arredo Urbano',
                'titles' => [
                    'Panchina danneggiata in {street}',
                    'Fontana non funzionante in {street}',
                    'Rifiuti e graffi sul monumento di {street}',
                    'Porta del parco scolodata in {street}',
                    'Marciapiede diviso dalla cancellata in {street}',
                ],
                'details' => [
                    'Seduta e\' instabile e le doghe sono arrugginite, chi si siede rischia di ribaltarsi.',
                    'La fontana e\' spenta da settimane, con acqua stagnante e zanzariera. Molti bambini ci giocano vicino.',
                    'Le panchine hanno le schienature asportate, non ci si puo\' piu\' sedere. Spesso gli anziani si fermano li\' per riposare.',
                ],
            ],
            [
                'id' => 'mobilita',
                'name' => 'Mobilita e Trasporti',
                'description' => 'Piste ciclabili, parcheggi, fermate, barriere architettoniche',
                'icon' => 'bicycle',
                'parent_id' => null,
                'sort_order' => 7,
                'type' => TicketTypeEnum::PUBLIC_TRANSPORT,
                'weight' => 8,
                'department' => 'Servizio Mobilita e Trasporti',
                'titles' => [
                    'Pista ciclabile interrotta in {street}',
                    'Parcheggio per disabili occupato in {street}',
                    'Fermata del bus senza pensilina in {street}',
                    'Barriera architettonica mancante in {street}',
                    'Marciapiede occupato da auto in {street}',
                ],
                'details' => [
                    'La pista ciclabile si interrompe all\'improvviso per colpa di un\'officina che ha occupato il tracciato.',
                    'Lo stall per disabili e\' occupato da un\'auto comune da almeno una settimana, in zona dove non ci sono altri parcheggi idonei.',
                    'La pensilina e\' sparita, gli utenti aspettano il bus sotto la pioggia.',
                ],
            ],
            [
                'id' => 'sicurezza',
                'name' => 'Sicurezza e Videosorveglianza',
                'description' => 'Punti ciechi, illuminazione di sicurezza, aree a rischio',
                'icon' => 'shield',
                'parent_id' => null,
                'sort_order' => 8,
                'type' => TicketTypeEnum::PUBLIC_SAFETY,
                'weight' => 6,
                'department' => 'Servizio Sicurezza Urbana',
                'titles' => [
                    'Punto cieco dell illuminazione in {street}',
                    'Videosorveglianza non funzionante in {street}',
                    'Area poco frequentata e buia in {street}',
                    'Vandalismo ripetuto in {street}',
                ],
                'details' => [
                    'Le telecamere di videosorveglianza non funzionano da una settimana, gli atti di vandalismo nella zona sono triplicati.',
                    'Di sera il tratto tra la scuola e la palestra e\' completamente buio: i ragazzi rientrano a piedi senza luce.',
                    'Nella notte tra venerdi\' e sabato ci sono stati due furti, la videosorveglianza risulta fuori uso.',
                ],
            ],
            [
                'id' => 'acqua',
                'name' => 'Acqua e Fognature',
                'description' => 'Perdite, allagamenti, tombini intasati, scarichi',
                'icon' => 'droplet',
                'parent_id' => null,
                'sort_order' => 9,
                'type' => TicketTypeEnum::SEWAGE_AND_DRAINAGE,
                'weight' => 7,
                'department' => 'Servizio Idraulica e Fognature',
                'titles' => [
                    'Perdita d acqua in {street}',
                    'Tombino intasato in {street}',
                    'Allagamento della carreggiata in {street}',
                    'Scarico fognario maleodorante in {street}',
                ],
                'details' => [
                    'Perdita d\'acqua costante dalla saracinesca: il contatore gira anche con tutte le utenze chiuse. L\'asfalto e\' ormai fratturato.',
                    'Il tombino e\' otturato e a ogni pioggia si allaga metta carreggiata, con il rischio di incidenti.',
                    'Lo scarico emette un odore fortissimo, probabilmente un problema alla rete fognaria del quartiere.',
                ],
            ],
            [
                'id' => 'energia',
                'name' => 'Energia e Impianti',
                'description' => 'Cabine elettriche, cavi aerei, guasti agli impianti',
                'icon' => 'bolt',
                'parent_id' => null,
                'sort_order' => 10,
                'type' => TicketTypeEnum::PUBLIC_BUILDINGS,
                'weight' => 4,
                'department' => 'Servizio Manutenzione Strade',
                'titles' => [
                    'Cavo aereo pericoloso in {street}',
                    'Cabina elettrica con sportello aperto in {street}',
                    'Impianto di semaforazione fuori uso in {street}',
                ],
                'details' => [
                    'Un cavo della rete elettrica pende a due metri dal suolo, a cavallo del marciapiede. Pericolo di elettrocuzione.',
                    'La cabina ha lo sportello divaricato, e\' accessibile a chiunque passa in strada.',
                ],
            ],
            [
                'id' => 'scuole',
                'name' => 'Edifici Scolastici',
                'description' => 'Scuole, asili, biblioteche e centri sociali',
                'icon' => 'academic-cap',
                'parent_id' => null,
                'sort_order' => 11,
                'type' => TicketTypeEnum::PUBLIC_BUILDINGS,
                'weight' => 5,
                'department' => 'Servizio Edilizia Scolastica',
                'titles' => [
                    'Finestra rotta nella scuola di {street}',
                    'Cortile della scuola danneggiato in {street}',
                    'Termocoperta rotta nell aula di {street}',
                    'Palestra comunale impraticabile in {street}',
                ],
                'details' => [
                    'Il vetro della finestra e\' rotto e non e\' stato protetto: pericolo per i bambini, l\'ho segnalato anche al custode.',
                    'Nel cortile manca la rete di protezione del campo da basket, i palloni finiscono in strada.',
                    'L\'inferriata del ballatoio e\' staccata, l\'accesso al primo piano e\' libero.',
                ],
            ],
            [
                'id' => 'ambiente',
                'name' => 'Ambiente e Rumore',
                'description' => 'Inquinamento acustico, rifiuti speciali, aree degradate',
                'icon' => 'globe',
                'parent_id' => null,
                'sort_order' => 12,
                'type' => TicketTypeEnum::ENVIRONMENTAL_REPORTS,
                'weight' => 5,
                'department' => 'Servizio Igiene Ambientale',
                'titles' => [
                    'Rumore notturno in {street}',
                    'Rifiuti speciali abbandonati in {street}',
                    'Fumo e scarichi sospetti in {street}',
                    'Impianto rumoroso di notte in {street}',
                ],
                'details' => [
                    'Attivita\' rumorosa che va avanti fino alle tre del mattimo: le pareti vibrano in casa. Ho gia\' segnalato, ma la situazione non cambia.',
                    'Pneumatici e materiali da costruzione abbandonati in un\'area di cantiere, dati al fuoco piu\' volte.',
                    'Dal locale accanto a {street} esce un fumo denso e maleodorante: potrebbe essere un impianto di scarico.',
                ],
            ],
        ];
    }

    /**
     * @return list<array{id: non-empty-string, name: non-empty-string, description: non-empty-string, icon: non-empty-string, parent_id: non-empty-string|null, sort_order: int, type: TicketTypeEnum, weight: int, department: non-empty-string, titles: non-empty-list<non-empty-string>, details: non-empty-list<non-empty-string>}>
     */
    private static function childCategories(): array
    {
        return [
            [
                'id' => 'strade-buche',
                'name' => 'Buche e Dossi',
                'description' => 'Buche nell\'asfalto, dossi artificiali danneggiati',
                'icon' => 'pothole',
                'parent_id' => 'strade',
                'sort_order' => 1,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'weight' => 9,
                'department' => 'Servizio Manutenzione Strade',
                'titles' => [
                    'Buca nel centro della carreggiata di {street}',
                    'Dossi sparito all incrocio di {street}',
                ],
                'details' => [
                    'Buca di circa dieci centimetri proprio in mezzo alla carreggiata, le due ruote ci cadono dentro.',
                    'Il dosso in gomma e\' stato divelto, non e\' piu\' segnalato il rallentamento.',
                ],
            ],
            [
                'id' => 'strade-marciapiedi',
                'name' => 'Marciapiedi e Accessibilita pedonale',
                'description' => 'Lastricato, cordoli, barriere e percorso pedonale',
                'icon' => 'walking',
                'parent_id' => 'strade',
                'sort_order' => 2,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'weight' => 6,
                'department' => 'Servizio Manutenzione Strade',
                'titles' => [
                    'Marciapiede sconnesso in {street}',
                    'Cordolo rotto in {street}',
                ],
                'details' => [
                    'Le lastre si muovono e formano gradini di 4 cm, per le sedie a rotelle e\' impraticabile.',
                    'Il cordolo e\' spaccato in piu\' punti, taglia i pedoni che escono dai portoni.',
                ],
            ],
            [
                'id' => 'strade-asfalto',
                'name' => 'Asfalto e Pavimentazione',
                'description' => 'Manto stradale, rattoppi, segnaletica orizzontale',
                'icon' => 'road',
                'parent_id' => 'strade',
                'sort_order' => 3,
                'type' => TicketTypeEnum::ROAD_MAINTENANCE,
                'weight' => 5,
                'department' => 'Servizio Manutenzione Strade',
                'titles' => [
                    'Rattoppo che si e\' staccato in {street}',
                    'Manto stradale sbriciolato in {street}',
                ],
                'details' => [
                    'Il rattoppo dell\'anno scorso si e\' staccato, lasciando un buco nel manto stradale.',
                    'L\'asfalto si sbriciola sotto le ruote, in piu\' punti del tratto.',
                ],
            ],
            [
                'id' => 'verde-alberi',
                'name' => 'Alberi e Siepi',
                'description' => 'Alberi pericolanti, potature, radici',
                'icon' => 'tree',
                'parent_id' => 'verde',
                'sort_order' => 1,
                'type' => TicketTypeEnum::PARKS_AND_GARDENS,
                'weight' => 7,
                'department' => 'Servizio Verde Pubblico',
                'titles' => [
                    'Ramo caduto sul marciapiede in {street}',
                    'Albero che ostruisce il passaggio in {street}',
                ],
                'details' => [
                    'Un grosso ramo e\' caduto bloccando il marciapiede; il tronco e\' squilibrato verso la strada.',
                    'L\'albero e\' cresciuto fino a occupare metta del marciapiede, restano pochi centimetri per passare.',
                ],
            ],
            [
                'id' => 'verde-parchi',
                'name' => 'Parchi e Aree Gioco',
                'description' => 'Giochi, panche, recinzioni, irrigazione',
                'icon' => 'puzzle',
                'parent_id' => 'verde',
                'sort_order' => 2,
                'type' => TicketTypeEnum::PARKS_AND_GARDENS,
                'weight' => 5,
                'department' => 'Servizio Verde Pubblico',
                'titles' => [
                    'Gioco rotto nel parco di {street}',
                    'Recinzione sfondata nell area giochi di {street}',
                ],
                'details' => [
                    'Il gioco ha una saldatura rotta e una parte sporgente, i bambini si fanno male spesso.',
                    'La rete della recinzione e\' stata tagliata, il cane di un vicino e\' entrato nell\'area giochi.',
                ],
            ],
            [
                'id' => 'rifiuti-abbandonati',
                'name' => 'Rifiuti Abbandonati',
                'description' => 'Discariche abusive, ingombranti, rifiuti pericolosi',
                'icon' => 'trash',
                'parent_id' => 'rifiuti',
                'sort_order' => 1,
                'type' => TicketTypeEnum::WASTE_COLLECTION,
                'weight' => 7,
                'department' => 'Servizio Igiene Ambientale',
                'titles' => [
                    'Frigorifero abbandonato in {street}',
                    'Materasso e rifiuti fuori dal cassonetto in {street}',
                ],
                'details' => [
                    'C\'e\' un vecchio frigo abbandonato in mezzo alla strada da settimane, occupa il passaggio.',
                    'Un materasso e\' stato lasciato sul marciapiede, nonostante i divieti di sosta.',
                ],
            ],
            [
                'id' => 'rifiuti-differenziata',
                'name' => 'Raccolta Differenziata',
                'description' => 'Raccolta che non avviene, contenitori danneggiati',
                'icon' => 'recycle',
                'parent_id' => 'rifiuti',
                'sort_order' => 2,
                'type' => TicketTypeEnum::WASTE_COLLECTION,
                'weight' => 4,
                'department' => 'Servizio Igiene Ambientale',
                'titles' => [
                    'Contenitori della differenziata rotti in {street}',
                    'Raccolta carta saltata in {street}',
                ],
                'details' => [
                    'Due contenitori hanno il coperchio divelto, i sacchi finiscono per terra.',
                    'La raccolta della carta non passa da una settimana, in questa via i container sono pieni.',
                ],
            ],
            [
                'id' => 'sicurezza-vandalismo',
                'name' => 'Vandalismo',
                'description' => 'Graffiti, danni ai beni pubblici, atti vandalici',
                'icon' => 'exclamation-triangle',
                'parent_id' => 'sicurezza',
                'sort_order' => 1,
                'type' => TicketTypeEnum::PUBLIC_SAFETY,
                'weight' => 4,
                'department' => 'Servizio Sicurezza Urbana',
                'titles' => [
                    'Graffiti sui muri di {street}',
                    'Vetrine e porte danneggiate in {street}',
                ],
                'details' => [
                    'Il muro del giardino pubblico e\' interamente coperto di graffiti, da almeno due notti.',
                    'I danni al gioco pubblico si ripetono: prima le colonnine, adesso la panchina.',
                ],
            ],
            [
                'id' => 'acqua-allagamenti',
                'name' => 'Allagamenti e Tombini',
                'description' => 'Pond, allagamenti, gronde scariche',
                'icon' => 'droplet',
                'parent_id' => 'acqua',
                'sort_order' => 1,
                'type' => TicketTypeEnum::SEWAGE_AND_DRAINAGE,
                'weight' => 4,
                'department' => 'Servizio Idraulica e Fognature',
                'titles' => [
                    'Pond sul marciapiede in {street}',
                    'Acqua ferma nel cortile di {street}',
                ],
                'details' => [
                    'La griglia del tombino si e\' incastrata e l\'acqua allaga mezzo marciapiede a ogni pioggia.',
                    'In cortile si forma un lago che non asciuga mai, con zanzare in estate.',
                ],
            ],
        ];
    }
}

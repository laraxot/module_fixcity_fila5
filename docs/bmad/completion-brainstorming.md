---
title: "Brainstorming per il completamento del progetto"
type: brainstorming
status: draft
updated: 2026-10-26
tags: [bmad, brainstorming, completamento, fixcity, rischi, decisioni]
qmd: "brainstorming completamento fixcity cinque perche rischi go-live domande al titolare idee scartate"
related: ["completion-gap-register.md", "completion-architecture.md", "completion-planning.md", "../stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md"]
---

# Brainstorming per il completamento del progetto

## Fase BMAD

Analysis. Serve a capire perche' le lacune ci sono, prima di correggerle, e a separare cio' che lo sviluppo puo' chiudere da cio' che decide il titolare. Nessuna idea qui e' una decisione: le decisioni vanno nel [registro DCP-200](../stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md).

## Cinque perche' sulle cause ricorrenti

### Perche' il sito mostra dati finti che sembrano veri

1. Perche' compaiono "Demo", "Name of the Region" e il numero verde 05 0505.
2. Perche' i JSON delle pagine e i modelli dei blocchi sono stati copiati dal modello Design Comuni, con i suoi segnaposto.
3. Perche' non esiste una fonte unica per i dati dell'ente, quindi ogni blocco ha il suo testo.
4. Perche' il lavoro di parita' ha misurato i pixel, non la verita' dei contenuti, e nessun controllo cercava i segnaposto.
5. Perche' nessuno aveva ancora deciso chi fornisce i dati dell'ente.
- Cura: una fonte unica con chiavi vuote, blocchi che si nascondono se il dato manca (DCP-289), e una regola nel controllo di go-live che cerca i segnaposto noti.

### Perche' i documenti dicono "fatto" quando non lo e'

1. Perche' una storia riporta una classe che non esiste e uno stato "done".
2. Perche' lo stato era scritto da chi aveva fatto il lavoro, senza l'output di un comando.
3. Perche' il registro dei documenti e' stato generato in blocco, con copie e numeri ripetuti.
4. Perche' un test passato e' stato letto come funzione completa (per esempio il 99,6% di parita' non e' mai stato misurato).
5. Perche' non c'era un'uscita obbligatoria del controllo per chiudere.
- Cura: la sezione Esito di ogni storia ha comandi e output; il registro distingue chiusa, parziale e non confermata; i report grezzi riportano MISURATO o DEDOTTO.

### Perche' un controllo verde non prova niente

1. Perche' PHPStan e' a zero.
2. Perche' analizza solo `Modules/`; `Themes/` e `laravel/app` restano fuori.
3. Perche' la configurazione e' protetta e il perimetro non si cambia.
4. Perche' una parte del lavoro sta nel tema, che e' un repository separato.
5. Perche' nessuno aveva scritto che il verde vale solo per il suo perimetro.
- Cura: ogni storia dice quale perimetro ha misurato; per i temi servono test del markup e misure nel browser.

### Perche' il proprietario non apre la sua segnalazione

1. Perche' "Vedi il dettaglio" porta a un 404.
2. Perche' il dettaglio pubblico usa una query che ignora chi guarda.
3. Perche' la query nasce per l'ospite, e il caso del proprietario non era un requisito scritto.
4. Perche' l'area personale e il dettaglio sono stati costruiti da storie diverse, senza una tabella di visibilita'.
5. Perche' il contratto di visibilita' non esisteva.
- Cura: la tabella di visibilita' del contratto C3 e un test su ogni combinazione.

## Rovesciare il problema: come potrebbe far male il giorno dell'apertura

| Scenario | Chi si fa male | Cosa lo previene | Stato |
|---|---|---|---|
| Un doppio clic crea due segnalazioni | Cittadino, operatori | Token di invio idempotente (C4) | DCP-286 in corso |
| La sessione scade e l'invio non fa nulla | Cittadino perde il testo | Messaggio e ritorno al wizard | DCP-286 in corso |
| La conferma promette una mail che non parte | Cittadino | Testo allineato a cio' che il sistema fa | DCP-284 fatta, testo in DCP-286 |
| La mail non parte mai perche' manca una coda o un relay | Cittadino, ente | Decisione di hosting e prova con posta reale | Aperto (DD-6, C8) |
| Il follower vede il codice di tracciamento di altri | Il proprietario | Mai mostrare il codice a chi non e' il proprietario | DCP-287 in corso |
| Il proprietario riceve 404 sulla sua segnalazione | Cittadino | Contratto C3 | DCP-287 in corso |
| Un utente non amministratore modifica altri utenti | Tutti | Policy sulla classe reale | DCP-285 fatta |
| Una pagina CMS esegue codice o espone un modello | Tutti | Allowlist e fallimento chiuso | DCP-283 fatta |
| I diritti dell'interessato non si possono esercitare | Cittadino, ente (sanzione) | Contatti DPO, registro richieste, cancellazione | Aperto (DEC-06, DEC-10, schema) |
| I testi legali sono incompleti o inventati | Ente | Il titolare fornisce i testi; il sito mostra un avviso onesto | Aperto (titolare) |
| Segreti nel repository | Ente | Rotazione e rimozione dall'indice di git | Aperto (DEC-28, serve conferma) |
| Un utente con tastiera o screen reader non completa il wizard | Cittadino (AgID) | Errori annunciati, focus gestito, un solo `main` | DCP-288/290/291 e giro successivo |
| Un host falso passa i controlli | Tutti | `TRUSTED_HOSTS` obbligatoria in produzione | Aperto (da decidere) |
| La ricerca nell'intestazione non cerca | Cittadino | Campo `q` e testo onesto | DCP-288 in corso |

## SCAMPER sulle soluzioni

- Sostituire: il numero verde finto non si sostituisce con uno vero inventato, si sostituisce con "nessun blocco" finche' l'ente non lo da'.
- Combinare: login, registrazione e reset password usano lo stesso schema Filament e lo stesso componente per gli errori, invece di tre implementazioni.
- Adattare: il pattern Design Comuni "form di ricerca" risolve il campo di tracciamento largo 16 px senza CSS nuovo di grande taglia.
- Modificare: il banner cookie fisso diventa un avviso in flusso; il testo e' solo informativo.
- Altro uso: la tabella di visibilita' del contratto C3 diventa anche la matrice dei test della policy.
- Eliminare: link `#`, "Prenota appuntamento" inventato, servizi non esistenti, pulsanti di valutazione che non salvano.
- Invertire: invece di aggiungere pagine per gli URL che danno 404, si correggono i link o il JSON del CMS.

## Domande per il titolare

Ordinate per quanto bloccano. La mia proposta e' la prima opzione.

| # | Domanda | Proposta | Blocca |
|---|---|---|---|
| 1 | Chi fornisce i dati dell'ente (nome, regione, indirizzo, PEC, URP, numero verde, social, orari) e in che forma? | Un file di configurazione per comune, compilato dall'ente | Footer, aiuto, barra alta, conferma |
| 2 | Il thread dei commenti di una segnalazione e' visibile a chi non e' collegato? | No: gli ospiti vedono solo il conteggio | Dettaglio segnalazione |
| 3 | La ricerca dell'intestazione cerca solo le segnalazioni o tutto il sito? | Solo le segnalazioni, con testo onesto; ricerca di sito dopo | DCP-288 |
| 4 | Posta in produzione: relay, coda e prova con posta reale | Un relay autenticato e un worker | Notifiche, reset password |
| 5 | Nome e contatto del DPO | Li da' l'ente | Informativa, diritti |
| 6 | Testi legali: chi li scrive e li approva | Il titolare con il DPO | Messa online |
| 7 | `TRUSTED_HOSTS` obbligatoria in produzione | Si | Sicurezza |
| 8 | Rotazione dei segreti tracciati e rimozione dall'indice | Si, con una procedura scritta | Sicurezza |
| 9 | Notifiche: filtro del personale per comune, assegnazione visibile al cittadino, follower, riassegnazioni, lingua (DD-1..DD-5) | Filtrare per comune, rivelare l'assegnazione senza nome, notificare ogni riassegnazione, lingua del destinatario | Ciclo di vita |
| 10 | Il cittadino puo' modificare, ritirare, integrare, riaprire, valutare la propria segnalazione? | Si, in storie piccole separate | Area personale |
| 11 | Lingua alla prima visita: dal browser o sempre italiano | Dal browser, con scelta ricordata | Prima impressione |
| 12 | Modifiche di schema (elenco nel documento di architettura) | Approvarle in blocco | Privacy, bozze, valutazioni |
| 13 | Cancellazione dell'account: capacita' dedicata e conseguenze sulle segnalazioni | Anonimizzare, non cancellare, le segnalazioni gia' lavorate | Diritti dell'interessato |
| 14 | Geocodifica: servizio esterno dal browser o proprio server | Proprio server con cache | Privacy, "Cerca un luogo" |
| 15 | La valutazione "Quanto sono chiare le informazioni" va salvata o tolta | Tolta finche' non salva davvero | Pulizia |

## Idee scartate o rimandate

- Chatbot e gamification come flusso principale: anti-riferimenti dichiarati nel prodotto.
- Ricostruire il tema da piu' agenti: scartato, una sola build coordinata.
- Spostare tutti i documenti in un colpo: vietato dopo l'errore del 2026-08-05.
- Pagine nuove nel tema per risolvere i 404: vietato dalla regola delle pagine.
- Un Service per i dati dell'ente: vietato; si usa un `Data` e una Action.
- Un tema scuro completo: rimandato, non e' un obbligo e oggi e' a meta (FO-5-33).
- Modificare i test per farli passare: scartato; si corregge il codice o si dichiara il test non valido con la prova.

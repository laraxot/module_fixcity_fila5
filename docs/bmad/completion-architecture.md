---
title: "Architettura per il completamento del progetto"
type: architecture
status: draft
updated: 2026-10-26
tags: [bmad, architettura, completamento, fixcity, front-office, contratti]
qmd: "architettura completamento fixcity contratti dati ente notifiche visibilita segnalazione invio idempotente policy utenti privacy accessibilita"
related: ["completion-gap-register.md", "completion-planning.md", "completion-brainstorming.md", "completion-front-office-blueprint.md", "../stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md"]
---

# Architettura per il completamento del progetto

## Fase BMAD

Solutioning. Questo documento non descrive codice nuovo: fissa i contratti che le storie di completamento devono rispettare, chi possiede ogni parte e quali scelte non spettano allo sviluppo. Le lacune che li motivano sono nel [registro](completion-gap-register.md); l'ordine di lavoro e' nel [piano](completion-planning.md).

## Cosa vuol dire completo

Un cittadino, anche poco esperto, puo' consultare i problemi del territorio, inviare una segnalazione da telefono dopo l'accesso, e capire in ogni momento in che stato e'. Un operatore della PA puo' prenderla in carico con una responsabilita' registrata. L'ente puo' mettere online il servizio senza segnaposto che sembrano dati veri, senza testi legali finti e senza promesse che il sistema non mantiene. Se un dato dell'ente manca, il blocco che lo mostra sparisce o dice la verita'. Non si inventa mai un numero di telefono, un indirizzo o un testo legale.

## Invarianti

Valgono per ogni storia. Le regole complete sono nella [mappa delle decisioni](../../../../../docs/second-brain/project/project-identity-and-decision-map.md).

| Invariante | Conseguenza pratica |
|---|---|
| La logica sta in Actions Spatie Queueable (`execute()`) | Nessun Controller, Service o Job nuovo. Una relazione Eloquent resta una relazione. |
| Le pagine sono JSON del CMS piu' blocchi | Un 404 non si ripara con un file in `Themes/*/resources/views/pages`. |
| Filament alla sua maniera | Widget Livewire con `Schema`, `Action`, `Notification`; classi `XotBase*`. |
| Dati sacri | Nessun `migrate:fresh`, `refresh`, `reset`, `db:wipe`, `migrate --force`. Ogni cambio di schema si chiede prima. |
| PHPStan si corregge nel codice | Mai la configurazione. Livello massimo, analizza solo `Modules/`. |
| Git solo in avanti | Nessun branch, nessun rollback come scorciatoia. Un file ritirato si rinomina `.old`. |
| Un autore per file | Lock con `bashscripts/lock/*.sh`. Altre sessioni lavorano nello stesso repository. |
| Il campo nascosto lo decide il client | Stato e priorita' iniziali sono imposti dal server; ogni proprieta' Livewire sensibile e' `#[Locked]`. |

## Confini tra proprietari

| Proprietario | Possiede | Non possiede |
|---|---|---|
| Fixcity | La segnalazione, il suo ciclo di vita, le notifiche del ciclo, il wizard, il pannello operatore | Mappe, identita', consensi, testi del sito |
| Geo | Mappe, geocodifica, selezione del punto | La segnalazione |
| User | Identita', ruoli, policy degli utenti, pagine di accesso | Consensi e cancellazione |
| Gdpr | Consensi, informative, banner cookie, richieste dell'interessato | Le pagine di accesso |
| Notify | Modelli e canali di notifica | Quando e a chi notificare (lo decide Fixcity) |
| Cms | Pagine JSON, risoluzione delle pagine, blocchi | Il contenuto dell'ente |
| Seo | Sitemap, metadati | I testi |
| Tenant | Contesto del comune e configurazione per comune | Il layout |
| Comment | Commenti e risposte (sessione parallela f5) | La visibilita' del thread ai non loggati: la decide l'utente |
| Rating, Media | Valutazioni, allegati | La promessa verso il cittadino |
| Sixteen | La veste del front office, i blocchi, il CSS | Chi puo' fare cosa |

La direzione e' sempre dal caso specifico verso la capacita' generica: Geo non conosce Fixcity, Notify non conosce il ciclo della segnalazione.

## Contratti da chiudere

Ogni contratto ha una storia. Lo stato e' quello del 2026-10-26.

### C1. Dati dell'ente, una sola fonte

- Problema: nome ente "Demo", regione "Name of the Region", numero verde copiato dal modello, contatti vuoti, social vuoti, link `#`.
- Contratto: una configurazione per comune con tutte le chiavi vuote di default, letta da un `Data` tipizzato in cartella `Datas` (plurale) tramite una Action che normalizza (telefono come `tel:` solo se numerico, URL solo http o https). I blocchi leggono solo quella fonte e si nascondono se il dato manca. Nessuna tabella nuova.
- Proprietario: Cms o Tenant, deciso dalla storia in base alla dipendenza.
- Storia: DCP-289, in corso. La maschera di modifica per l'ente (Section, TextInput, Repeater) e' un disegno nel [blueprint](completion-front-office-blueprint.md), non codice: richiede una pagina impostazioni da decidere.

### C2. Notifiche del ciclo di vita

- Contratto: eventi ricevuta, approvazione, rifiuto, prima assegnazione, chiusura. Canali database e posta verificata. Lingua del destinatario. Il motivo del rifiuto e' limitato a 300 caratteri.
- Storia: DCP-284, chiusa e verificata (21 test, 265 asserzioni). Nessuna mail reale e' stata spedita e provata: `MAIL_MAILER` di sviluppo e' `sendmail`.
- Aperto, da decidere: DD-1 filtro del personale per comune; DD-2 rivelare l'assegnazione al cittadino; DD-3 follower e codice di tracciamento; DD-4 notificare ogni riassegnazione; DD-5 lingua del destinatario per le notifiche esistenti; DD-6 posta reale e coda.
- Regola per la conferma del wizard: il testo promette solo cio' che il sistema fa. Vedi C4.

### C3. Chi vede quale segnalazione

Contratto di visibilita' del dettaglio, da realizzare in DCP-287 con la policy esistente e senza controller nuovi.

| Chi | Segnalazione pubblica | Segnalazione non pubblica |
|---|---|---|
| Ospite | Campi pubblici | 404 |
| Altro cittadino | Campi pubblici | 404 |
| Follower | Campi pubblici, mai il codice di tracciamento | 404 |
| Proprietario | Tutto cio' che vede nell'area personale, e il suo codice | Lo stesso: oggi riceve 404, ed e' un buco funzionale |
| Personale PA | Pannello operatore | Pannello operatore |

- Non si espongono mai codice-capability, `owner_id` ed email degli altri.
- Da decidere (privacy): il thread dei commenti di una segnalazione e' visibile a chi non e' collegato? Oggi un'altra sessione lo ha reso leggibile agli ospiti. La proposta e' mostrare agli ospiti solo il conteggio.

### C4. Invio idempotente e conferma onesta

- Problema: un doppio clic su "Invia" crea due segnalazioni; con la sessione scaduta il tasto non fa nulla; la conferma promette una mail.
- Contratto: un token di invio `#[Locked]` generato al mount, un blocco `Cache::lock` e un controllo "gia' creata per questo token" che restituisce la stessa conferma; pulsante disabilitato durante l'invio (solo come aiuto, non come garanzia); sessione scaduta con messaggio e ritorno al wizard. Nessuna migrazione: il token vive in cache.
- Storia: DCP-286, in corso.

### C5. Autorizzazione degli utenti

- Contratto: la policy e' registrata sulla classe di autenticazione reale e copre le sottoclassi. Amministrano `super-admin`, `admin` e `user::admin`. Gli altri vedono e aggiornano solo se stessi. Nessuno si cancella da solo. Un `admin` non puo' modificare un `super-admin`. Le abilita' collettive hanno un metodo esplicito, perche' Filament concede quelle senza metodo.
- Storia: DCP-285, chiusa (23 test nella matrice). Resta da decidere la capacita' di cancellare il proprio account (DEC-10).

### C6. Contenuto reso in modo sicuro

- Problema: `PageSlugMiddleware` era aperto per errore, `ResolvePageAction` accettava modelli arbitrari, il modello di notifica compilava Blade da dati modificabili.
- Contratto: ogni punto che trasforma testo in codice o risolve una classe dal nome usa una allowlist esplicita, e fallisce chiuso.
- Storia: DCP-283, chiusa e verificata nel perimetro. Resta da decidere cosa fare dei costrutti Blade nei modelli di produzione (oggi ci sono zero modelli): si leggono i log dopo il deploy e si aggiungono solo i costrutti visti, ognuno con un test.

### C7. Privacy e prova del consenso

- Contratto: ogni consenso salvato ha utente, versione del documento, data e indirizzo; la revoca conserva la riga come traccia. Oggi la prova della registrazione non e' salvata (A-04) e la revoca non puo' registrare lo user agent.
- Schema da chiedere (non modificato): colonna `revoked_user_agent` in `consents`; persistenza della prova di consenso in registrazione; registro delle richieste dell'interessato.
- Storie: DCP-285 (revoca, fatta), le altre dopo l'approvazione dello schema.

### C8. Operativita'

- Scheduler, coda, backup, posta reale, `TRUSTED_HOSTS` obbligatoria in produzione, rotazione dei segreti tracciati in git (DEC-28). Nessuna di queste cose si chiude con codice: servono decisioni di hosting e del titolare. Sono nel calendario del [piano](completion-planning.md).

### C9. Accessibilita' e lingue

- Contratto: errori di campo con `aria-invalid` e `aria-describedby`; dopo un invio fallito il focus va al primo campo non valido con un avviso `role="alert"` che dice quanti sono; un solo `main` con id e `tabindex="-1"`; nessuna chiave grezza in nessuna delle quattro lingue (it, en, de, es); nomi di attributo veri nelle validazioni; nessun `autofocus` su login e reset.
- Storie: DCP-288, DCP-290, DCP-291, e un giro successivo per il wizard e la mappa.

### C10. Una sola build del tema

- Contratto: nessun agente ricostruisce Sixteen. Un solo coordinatore lancia la build con `flock`, perche' un'altra sessione ricostruisce lo stesso tema. Ogni storia che tocca il CSS sorgente dichiara "serve build" con l'elenco esatto delle regole e la misura da fare dopo.

## Schema da chiedere

Nessuna di queste modifiche e' stata fatta. Si chiede prima, in un unico blocco.

| Cosa | Perche' | Lacuna |
|---|---|---|
| Colonna `revoked_user_agent` in `consents` | Prova della revoca | D-02 |
| Persistenza della prova di consenso in registrazione | Art. 7 GDPR | A-04 |
| Registro delle richieste dell'interessato | Accesso, cancellazione, portabilita' | A-05, D-01, D-09 |
| Indice unico sul codice della segnalazione | Collisioni | A-24 |
| Bozze del wizard | Non perdere i dati | FO-3-20 |
| Salvataggio della valutazione della pagina | Il widget oggi non salva | B-05 |

## Prove richieste a ogni storia

1. Un test Pest scritto prima, rosso e poi verde, lanciato solo con `bashscripts/tools/pest-scratch-db.sh` (database usa e getta).
2. PHPStan a zero sui file toccati, dalla cartella `laravel/`.
3. Per le modifiche visibili: misura nel browser sull'istanza di prova, a tre larghezze.
4. Ogni fatto marcato MISURATO o DEDOTTO. "Fatto" senza l'output del comando non vale.

## Debiti riconosciuti

- PHPStan analizza solo `Modules/`: `Themes/` e `laravel/app` non sono coperti. Un PHPStan verde non prova nulla sul tema.
- I vecchi file `docs/second-brain/project/{missing-work-index,bmd-brainstorming-remaining-work,bmad-architecture,bmad-stories}.md` non sono affidabili e uno contiene una credenziale in chiaro (DCP-275): non vanno copiati.
- Segreti tracciati in git (credenziali FTP, segreti OAuth, copie `.env.*.bak`, chiave TLS): i valori non sono riportati in nessun documento.
- Il riordino dei documenti in blocco e' vietato dopo l'errore fatale del 2026-08-05: si sposta un file alla volta, con `git mv` e riscrittura dei collegamenti in ingresso.
- Esistono lock orfani vecchi di settimane in piu' cartelle di documenti: si rimuovono solo dopo aver provato che il processo e' morto e che il file e' cambiato dopo.

---
id: story-514-public-navigation-contract
slug: 508-public-navigation-contract
title: "STORY-514 — Il contratto della navigazione pubblica: cosa un visitatore anonimo deve vedere"
description: "Definisce, in modo verificabile, cosa la navigazione pubblica di FixCity deve mostrare a un visitatore non loggato, per ogni percorso e per ogni locale. E' il documento di riferimento: STORY-515 confronta questo contratto con la realta' misurata e propone le correzioni."
document_type: story
category: frontend
status: in_progress
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
epic: EPIC-FRONTOFFICE
points: 5
priority: Must

# NON CREATE: `gh` non e' installato. Le URL sotto seguono la convenzione del
# repository ma NON esistono su GitHub. Non aprirle come se fossero reali.
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/514"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/515"

related:
  - ../../../../bashscripts/ai/wiki/concepts/user-journeys.md
  - ../../../../bashscripts/ai/wiki/concepts/architectural-invariants.md
  - ../../../../bashscripts/quality-gates/audit-public-navigation.sh
  - ../../app/Policies/TicketPolicy.php
  - ../../routes/web.php
---

# STORY-514 — Il contratto della navigazione pubblica

## Perche' questo documento esiste

Il prodotto e' una piattaforma di segnalazione civica. Il suo atto principale e' **un
visitatore che non ha un account apre il sito e segnala un problema**. Tutto il resto
— backoffice, KPI, SLA, mappe, GDPR — e' strumentale a quello.

Non c'era nessun documento che dicesse cosa quel visitatore deve incontrare. C'era
`user-journeys.md`, che descrive i sei tipi di utente, ma risponde a «chi e' l'utente»,
non a «cosa deve apparire». Questo documento risponde a «cosa deve apparire», in modo
**verificabile**: ogni voce e' un'affermazione che uno script puo' confermare o
smentire, non un auspicio.

**Stato della verifica:** `bash bashscripts/quality-gates/audit-public-navigation.sh`
implementa le condizioni C1-C9 di questo contratto. Il confronto con la realta' e' in
STORY-515.

## Il vincolo architetturale che il contratto deve rispettare

Dalla regola del progetto, che non e' una scelta di gusto:

> Backoffice = **Filament**. Frontoffice = **Folio + Volt + Filament**.
> **Mai** Controller HTTP. Logica in `app/Actions/*Action.php`.

Quindi ogni percorso pubblico di questa tabella deve essere una **pagina Folio**
(`resources/views/pages/**`) o un **componente Volt**, e mai una closure in
`routes/web.php` che renderizza una view bypassing Folio. Una rotta che duplica un
path già servito da Folio non è un errore di stile: **è il meccanismo con cui Folio
perde il frontoffice senza che lo si veda**.

## Percorso 1 — La root anonima

`GET /` e `GET /{locale}` (con locale in `it`, `en`, `de`)

| # | Deve | Verificabile come |
|---|---|---|
| C1 | HTTP 200 | codice di risposta |
| C2 | Il layout applica il CSS del tema attivo | `document.styleSheets.length >= 1` e un foglio con regole `layout`/`container` |
| C3 | Header con il nome del Comune e la navigazione | `<header>` presente, con almeno 3 link di navigazione |
| C4 | Un richiamo al fatto che questo sito serve a segnalare un problema | testo non vuoto nella prima sezione |
| C5 | Il selettore di lingua funziona e cambia prefisso | `/it` → `/en` → `/de`, tutte 200 |
| C6 | Nessun errore in console, nessuna richiesta fallita | `page.on('console'|'requestfailed')` |
| C7 | Nessuna chiave di traduzione greffa nel testo | nessuna corrispondenza `\b\w+\.\w+\.\w+\b` nel testo visibile |
| C8 | Zero errori 5xx su tutto il percorso | nessun 5xx nel log |

## Percorso 2 — La creazione della segnalazione

`GET /{locale}/tickets/create`

| # | Deve | Verificabile come |
|---|---|---|
| C9 | **raggiungibile da anonimo, senza login** | 200, non 302 verso `/auth/login` |

C9 e' il punto piu' discusso del contratto, quindi va detto chiaramente: **oggi non e'
un difetto, e' una scelta non scritta.** `TicketPolicy::create()` concede la creazione a
chiunque abbia un id autenticato:

```php
return $user->hasPermissionTo('ticket.create')
    || $user->getAuthIdentifier() !== null;
```

E la pagina `/{locale}/tickets/create` redirige a `/{locale}/auth/login`. Sono due
comportamenti che si contraddicono solo in apparenza: la policy riguarda il backoffice,
la pagina riguarda il frontoffice. Ma **il risultato per l'utente e' che per segnalare
deve crearsi un account**, e non c'e' scritto da nessuna parte se e' la scelta voluta.

Le tre opzioni, e ognuna ha un costo diverso:

| Opzione | Effetto | Costo |
|---|---|---|
| **A.** Segnalazione aperta all'anonimo | il prodotto funziona come descritto | servono CAPTCHA e rate limiting, e una modalita' per l'anonimo |
| **B.** Account obbligatorio, scritto | il comportamento resta, ma e' dichiarato | nessuno, e onestamente e' una scelta legittima |
| **C.** Segnalazione anonima +/account facoltativo | il meglio dei due | richiede il doppio percorso di intake |

**Non e' una decisione che prendo io.** Fino a che non e' scritta, ogni agente che
legge `TicketPolicy` e corregge «l'|| getAuthIdentifier() nonnull' apre troppo» rompe
il modello, e ogni agente che legge la pagina e «la rende pubblica» cambia il prodotto.

## Percorso 3 — L'elenco delle segnalazioni

`GET /{locale}/tickets`

| # | Deve | Verificabile come |
|---|---|---|
| C10 | 200 e un elenco, non unVUOTO | testo visibile > 120 caratteri |
| C11 | ogni voce mostra stato e data | presenza dei campi per riga |
| C12 | il nome pubblico `route('tickets.list')` risolve | `route:list` contiene `tickets.list` |

## Percorso 4 — Il tracciamento della propria segnalazione

`GET /{locale}/tickets/track/{code}`

| # | Deve | Verificabile come |
|---|---|---|
| C13 | 200 | |
| C14 | con un codice inesistente risponde in modo gentile, non con un 500 | 404 o messaggio, mai 5xx |
| C15 | non espone dati di altri cittadini | nessun identificativo personale senza il codice |

C15 e' l'unico requisito di sicurezza di questo contratto, e non e' verificabile senza
database popolato. Va verificato per primo quando si avra' un ambiente di test.

## Percorso 5 — L'area personale

`GET /{locale}/area-personale/*`

| # | Deve | Verificabile come |
|---|---|---|
| C16 | **richiede autenticazione** | 302 verso `/auth/login` per l'anonimo |
| C17 | le pagine esistono per ogni locale | stesse rotte sotto `it`, `en`, `de` |

## Percorso 6 — Il backoffice

`GET /{modulo}/admin`

| # | Deve | Verificabile come |
|---|---|---|
| C18 | 302 verso `/{modulo}/admin/login` per l'anonimo | |
| C19 | esiste per ogni modulo che espone un pannello | `route:list` |
| C20 | `Dashboard` estende `XotBaseDashboard` | `ReflectionClass`, non `grep` |

## Percorso 7 — L'API pubblica

`GET /{locale}/api/tickets/geojson`

| # | Deve | Verificabile come |
|---|---|---|
| C21 | 200 con GeoJSON valido | `json.loads` e `type == FeatureCollection` |
| C22 | non espone dati personali | nessun `owner_id`, `email`, `name` nel payload |
| C23 | ha rate limiting | header `X-RateLimit-*` o middleware dichiarato |

**C22 e` la piu` importante di tutto il contratto** per un prodotto che segnala
problemi a indirizzi civici. Va verificata con un database popolato.

## Percorso 8 — Coerenza multilingua

| # | Deve | Verificabile come |
|---|---|---|
| C24 | nessuna parola italiana hardcoded nel **markup** delle blade | le stringhe visibili passano da `__()` o sono contenuto CMS |
| C25 | ogni chiave usata esiste nel file di traduzione | chiave assente = 0 |
| C26 | le stesse pagine esistono in tutte le locali | stessi path sotto `it`, `en`, `de` |
| C27 | nessun locale nel nome del file | nessun `header_bi5.blade.php` |

**C24 ha una distinzione che va tenuta:** il **markup** e' interfaccia e va tradotto; il
**contenuto CMS** (i titoli delle pagine, le descrizioni dei servizi) e' in italiano per
scelta editoriale e va lasciato cosi'. Tradurre il contento e' un lavoro editoriale, non
un bug.

## Percorso 9 — Le condizioni che rendono tutto il resto verificabile

| # | Deve | Verificabile come |
|---|---|---|
| C28 | il database e' raggiungibile dal processo che serve il sito | una query banale |
| C29 | esiste un utente concesso per i test | seed |
| C30 | l'asset del tema e' compilato, o il dev server e' attivo | manifest presente **oppure** `hot` con processo vivo |
| C31 | nessun file funzionale contiene un marker di merge | `audit-merge-conflicts.sh`, classe `BLOCCANTE` |

C30 e' la piu` insidiosa: **manifest assente e dev server morto** e' indistinguibile da
una pagina senza stile, e produce una pagina che si vede male senza nessun errore. Il
contratto la dichiara perche' il sintomo e' silenzioso.

## AC

- [x] Ogni percorso pubblico ha le sue condizioni verificabili
- [x] Le condizioni sono numerate e referenziabili da una story di correzione
- [x] Il vincolo architetturale (Folio, non Controller) e' dichiarato come parte del
      contratto, non come nota a margine
- [x] C9 dichiarato come **scelta da prendere**, non come difetto da correggere
- [x] C22 (dati personali nell'API) dichiarato come requisito di sicurezza
- [x] C24 distingue markup da contenuto CMS
- [x] `audit-public-navigation.sh` implementa il contratto ed e' eseguibile

## Change log

- **2026-09-27** — creazione. 31 condizioni su 9 percorsi, 6 tipi di utente. Ogni
  condizione e' un'affermazione che uno script puo' smentire. Il confronto con la
  realta' misurata e' in STORY-515.

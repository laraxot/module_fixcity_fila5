---
title: "BMAD 07 — UI/UX FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, ui, ux, sixteen, design-comuni, fixcity]
module: Fixcity
qmd: "bmad ui ux wizard responsive accessibility sixteen fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-citizen.md
  - ../../../../Themes/Sixteen/docs/bmad/README.md
  - 08-security.md
---

# UI/UX

**Perché:** il cittadino usa il frontoffice; parity Design Comuni e accessibilità
sono parte del prodotto, non un optional post-release.

## Ownership

- Modulo FixCity: comportamento, schema, copy keys, stati.
- Tema Sixteen: layout, CSS, componenti visuali, report parity.

## Passi

1. Percorso cittadino a 320 / 768 / 1440px: privacy, dati, posizione, upload,
   errori, riepilogo, conferma, tracking.
2. Backoffice PA: lista, dettaglio, assign, change status, toast/errori.
3. Tastiera, focus, contrasto, messaggi errore, screen reader sui punti critici.
4. Nessun header/CTA duplicato tra modulo e tema.
5. Screenshot/report in `Themes/Sixteen/docs/` con link alla story FixCity.

## Evidenza runtime aggiornata — 2026-09-27

La route `/segnalazioni` attiva è la pagina CMS `segnalazioni.index`: usa la query live del modulo, `/api/tickets/geojson`, filtri tipo/stato applicati alle card e paginazione server-side da 20 elementi. Il browser verifica layout e interazione Mappa/Elenco a 320/768/1440 px, titolo, CTA, assenza di overflow ed errori JS; Pest copre filtri, paginazione e assenza del capability code. Tile reali sono caricati, ma marker/popup richiedono ticket geolocalizzati non presenti nell'ambiente corrente.

Il tracking pubblico valido e il lookup owner sono stati verificati su database SQLite temporaneo: il guest vede stato/timeline pubblici senza capability code; l'owner autenticato vede il proprio codice via ID; il guest via ID riceve 403. La pagina di errore IT/EN è stata controllata in browser. Report con misure e limiti: [audit runtime FixCity](../ui-ux-runtime-audit-2026-09-26.md) e [report owner Sixteen](../../../../Themes/Sixteen/docs/bmad/fixcity-ui-ux-runtime-audit.md).

Wizard autenticato provato a 320/768/1440 px e IT/EN: title CMS e step localizzati, nessun overflow o chiave grezza; la privacy è obbligatoria e Space avanza allo step dati. Se il consenso manca, `aria-invalid` e `aria-describedby` collegano il messaggio al checkbox e il focus torna al controllo.

Il riepilogo non chiede più nome, codice fiscale, telefono o email: erano campi senza persistenza, ridondanti per un flusso autenticato. Chromium ha completato privacy, dati, selezione file, riepilogo e submit con utente e database SQLite effimeri: redirect a `/it/tickets/confirmation`, title “Segnalazione inviata”, codice tracciamento, email owner e zero errori HTTP/JavaScript. Una query DB ha verificato il ticket sintetico e il file Media associato in `attachments`. Rimosso `imageEditor()` perché Chromium riproduceva `this.editor.getCroppedCanvas is not a function`; l'upload diretto risolve il blocco senza togliere la raccolta immagini. Aggiunte le traduzioni mancanti IT/EN per il conteggio step che appariva come chiave grezza.

La revisione del riepilogo ha trovato altri placeholder UI (`empty5`, `review_name` e label simili). I cataloghi IT sono stati corretti, aggiunto quello EN e rimossa la testata vuota. Chromium sul percorso canonico `/it|en/tickets/create` conferma le label corrette a 320/768/1440 px, senza chiavi grezze, overflow o errori JS; Pest copre IT/EN. Per G-25 la view attiva Sixteen ora localizza il footer e usa destinazioni esistenti per home, invio e tracking; sono stati rimossi dati comunali inventati e link `#`. Chromium ha verificato footer IT/EN a 320/768/1440 px; il test mirato passa (2 test / 16 asserzioni) e la suite FixCity passa (369 test / 1.570 asserzioni) con SQLite isolato. In `.env.testing` le credenziali MariaDB restano placeholder per scelta fail-closed: per SQLite temporaneo costruire lo schema sotto `database/tmp/` e passare `DB_CONNECTION=sqlite`, `DB_DATABASE` e `DB_DATABASE_USER` con lo stesso nome relativo, senza alterare `.env.testing`. Recapiti e pagine legali restano una configurazione mancante lato ente.

G-27: il guest che apre il wizard da `/it` o `/en` resta nella lingua richiesta quando viene mandato al login; la sessione conserva `url.intended` sul wizard e le richieste JSON restano 401. Pest: 3 casi / 15 asserzioni; suite FixCity: 372 test / 1.585 asserzioni. Chromium ha seguito l'ingresso guest a 320/768/1440 px: login e footer localizzati, zero overflow o errori JS. Dettaglio e limiti nel workflow [visitatore](actor-visitor.md) e nello [story CMS](../../../../Cms/docs/bmad/stories/STORY-CMS-FOLIO-LOCALE-AUTH-REDIRECT.md).

Rimangono verifiche tastiera/screen reader complete per wizard, rating e flussi PA in browser, contrasto misurato, marker su ticket geolocalizzato e smoke test MySQL/staging; il browser SQLite non sostituisce questi controlli.

## Verifica recente del gate privacy — 2026-09-27

La route `/it/privacy` senza policy risponde 503 localizzato; la policy di test
Markdown viene renderizzata dopo rimozione HTML, e le prove Livewire negano invii
forgiati se la policy manca. Suite FixCity corrente: 385 test / 1.683 asserzioni.
Questi test HTTP non sono una verifica visuale. Chromium non è avviabile in questo
runtime perché `ldd` segnala mancanti `libatk-1.0.so.0`, `libatk-bridge2.0.so.0`,
`libasound.so.2`, `libXdamage.so.1` e `libatspi.so.0`; non sono stati prodotti nuovi
screenshot della pagina privacy. Gli screenshot sulle altre pagine citati sopra sono
prove storiche e non certificano la route privacy modificata.

La semantica di “ho letto e compreso” e l'eventuale persistenza della prova sono
ancora da approvare col titolare, come registrato nella [story privacy acknowledgement](../stories/STORY-FIXCITY-PRIVACY-ACKNOWLEDGEMENT-AUDIT.md).

## Gate

Nessun duplicato markup/CTA; focus/errori/contrasto verificati o gap esplicito.

## Output

Report UI/UX tema + story aggiornata.

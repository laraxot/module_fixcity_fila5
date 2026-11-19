---
id: dcp-456
title: "Wizard tickets/create passo 1: avviso privacy breve + link, non l'informativa integrale"
type: story
epic: dc-parity-v2
status: review
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-11-12
tags: [wizard, privacy, gdpr, design-comuni, passo-1, parity]
qmd: "dcp-456 tickets create wizard passo 1 privacy gdpr avviso breve link informativa integrale parity"
depends_on: [Modules/Fixcity/docs/stories/dcp-46-wizard-passo1-privacy-markup.story.md, Modules/Fixcity/docs/stories/dcp-35-tickets-create-privacy-dati-coordinamento.story.md]
related: [Modules/Fixcity/docs/stories/dcp-130-informativa-privacy-art13-e-pagina-servita.story.md]
issues: ["https://github.com/laraxot/theme_sixteen_fila5/issues/98"]
discussions: ["https://github.com/laraxot/theme_sixteen_fila5/discussions/96"]
reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html
local: http://192.168.1.40:8000/it/tickets/create
theme_story: "../../../../Themes/Sixteen/docs/stories/ticket-create-privacy-design-comuni-decision.story.md"
---

# DCP-456: passo 1 privacy — avviso breve invece dell'informativa integrale

Decisione condivisa con il tema Sixteen: [story visuale ed ECC](../../../../Themes/Sixteen/docs/stories/ticket-create-privacy-design-comuni-decision.story.md) · [discussion #96](https://github.com/laraxot/theme_sixteen_fila5/discussions/96).

## Richiesta (2026-10-14)

Su `http://192.168.1.40:8000/it/tickets/create` (utente loggato) "c'è troppo testo": il
riferimento `segnalazione-01-privacy` mostra poco testo con link di approfondimento.

## Perché (root cause)

`TicketForm::getGdprHtml()` passa a `fixcity::components.gdpr-notice` l'intera
informativa privacy pubblicata (`$policyHtml` = markdown completo, tutti i paragrafi)
e la vista lo stampa tutto nel passo 1. Il riferimento Design Comuni mostra invece:

1. un breve avviso operativo centrato sul sistema FixCity e sulla segnalazione;
2. link separati all'informativa privacy, ai termini e alla cookie policy;
3. la checkbox "Ho letto e compreso l'informativa sulla privacy".

Il testo integrale vive già sulla pagina dedicata `/privacy` (CMS `privacy.index`,
blocco `legal_document`): duplicarlo nel wizard allunga il passo, distrae dal compito
e crea una seconda copia di un contenuto mantenuto altrove.

Esisteva già la chiave `fixcity::ticket.gdpr_notice.text`, ma il copy ereditato
attribuiva impropriamente il trattamento al Comune tramite `:municipality`.
La chiave è stata mantenuta per compatibilità e resa neutra in tutte le lingue.

## Decisione

- Passo 1 = avviso breve `gdpr_notice.text` neutro, senza `Comune`, `ente` o
  qualifica di titolare/responsabile, più link localizzati verso `/privacy`,
  `/terms` e `/cookie-policy` + checkbox.
- Informativa non pubblicata → resta l'alert `privacy.not_configured` e checkbox
  disabilitato (comportamento attuale, invariato).
- Mantenere `gdpr_notice` (+`text_no_name`) coerente e nativo in tutte le lingue.
- `getGdprHtml()` non renderizza più il markdown della policy: serve solo a sapere se
  l'informativa è pubblicata.

## Differenza decisionale consolidata — 2026-11-12

La differenza tra il comportamento precedente e quello adottato è registrata nella
[Discussion #96](https://github.com/laraxot/theme_sixteen_fila5/discussions/96):

| Prima | Dopo | Perché |
| --- | --- | --- |
| Il passo 1 stampava l'informativa privacy completa | Avviso breve, link localizzato a `/privacy` e checkbox | Il passo deve orientare all'azione, non duplicare il documento legale |
| Il wizard era una seconda fonte del testo normativo | `/privacy` resta la fonte completa CMS | Una sola fonte riduce divergenze e manutenzione |
| L'utente autenticato scorreva molti paragrafi prima del consenso | Informazione essenziale subito, approfondimento su richiesta | Progressive disclosure e minore carico cognitivo |

La riduzione vale per il wizard autenticato e per lo schema Filament condiviso; non cambia
la validazione del consenso, la policy legale, il CMS o il flusso anonimo.

## Fuori scope

- Contenuto dell'informativa (`policy.md`, template legali).
- Struttura wizard, step dati, riepilogo (DCP-33/34/46).

## Verifica

- [x] `php artisan view:cache` exit 0
- [x] Pest `Modules/Fixcity/tests` wizard/privacy (narrowest)
- [x] PHPStan `Modules/Fixcity` file toccati
- [x] Render vista via tinker: it/en identici al riferimento (avviso + link)
- [ ] Playwright autenticato: screenshot passo 1 vs `segnalazione-01-privacy` (serve account di prova DCP-98)

## Dev Agent Record

**2026-10-14 — implementato**

- `TicketForm::getGdprHtml()`: non renderizza più `$policy` in markdown; passa alla
  vista `hasPolicy` (solo per l'alert "non configurata"), `notice` (testo breve
  neutro) e gli URL localizzati di privacy, termini e cookie policy.
  Rimossi import `Str`/`preg_replace` diventati inutili.
- `components/gdpr-notice.blade.php`: nuovo contratto — avviso breve + link
  `privacy.detail_prefix`/`privacy.link` verso `/privacy` localizzata; alert
  `not_configured` invariato quando la policy non è pubblicata.
- Lang: `gdpr_notice` contiene copy native equivalenti e neutrali in `it`, `en`,
  `de` ed `es`; nessun placeholder dell'ente viene interpolato.
- Test di regressione in `CreateTicketWizardWidgetTest` ("privacy step notice"):
  mock della policy con marcatore → `assertSee` avviso breve, `assertSeeHtml`
  `/it/privacy`, `assertDontSee` marcatore (l'integrale non è più inline).
- Stesso schema per il back office (`getFormSchema` → `getPrivacySchema`): anche lì
  il consenso mostra ora l'avviso breve — coerente, la policy resta su /privacy.

**Gates**

- `view:cache` OK; Pint fixato `TicketForm.php`; PHPStan zero su entrambi i file.
- Pest wizard widget: 5/6 — unico rosso `assertOk` su `/it/tickets/confirmation`
  (404 in test-env), **preesistente** e non legato alla modifica (pagina CMS non
  risolta nel tenant di test, vedi audit FO).
- Unit (`TicketFormLangParity`, `DataStepDesignComuni`, `Lang coverage`,
  `WizardSummarySchema`): 25/25 verdi.
- Tinker: `gdpr-notice` it/en renderizza testo identico al riferimento
  (`Il Comune di Firenze gestisce…` / `The Municipality of …`).

**Aperto**: verifica visiva autenticata (Playwright) con account DCP-98.

## Nota di un'altra sessione (2026-11-12, aggiunta in coda, il resto della story non è toccato)

- Il numero `dcp-456` era stato preso anche da una mia bozza sullo stesso tema; questa story era nata prima, quindi il numero resta a lei. La mia è diventata
  [DCP-457](./dcp-457-guardia-e-misure-del-primo-livello-informativa-passo-1.story.md): analisi misurata (1.729 parole contro 98 del riferimento), guardia
  `Modules/Fixcity/tests/Unit/PrivacyStepIsATwoLayerNoticeTest.php` (12 test, 54 asserzioni, 4 lingue, verdi sul codice di questa story) e decisioni aperte.
- Da verificare qui: `created`/`updated` e «Richiesta (2026-10-14)» riportano il 2026-10-14, ma il file è nato il 2026-11-12; la `discussions` indicata è la #69 del repo del
  modulo, mentre l'audit del front office è nella #94 del tema (https://github.com/laraxot/theme_sixteen_fila5/discussions/94).
- «sulla base dell'articolo 13 del Regolamento» (copy del riferimento): l'art. 13 è l'informativa, non una base giuridica; non è una modifica da fare qui, è la decisione
  aperta DCP-134 (vedi DCP-457, «Decisioni aperte»).

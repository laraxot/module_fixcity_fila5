---
id: dcp-457
title: "DCP-457: guardia, misure e decisioni aperte del primo livello dell'informativa nel passo 1 (segue DCP-456)"
type: story
status: in-progress
module: Fixcity
created: 2026-11-12
updated: 2026-11-12
tags: [bmad, story, wizard, passo-1, privacy, informativa, gdpr, art-12, design-comuni, guardia, i18n]
qmd: "passo 1 wizard tickets create troppo testo informativa a due livelli primo livello guardia PrivacyStepIsATwoLayerNoticeTest gdpr-notice getGdprHtml art 12 art 13 base giuridica segnaposto ente"
related:
  - ./ticket-create-privacy-avviso-breve.story.md
  - ./dcp-46-wizard-passo1-privacy-markup.story.md
  - ./dcp-35-tickets-create-privacy-dati-coordinamento.story.md
  - ./dcp-134-base-giuridica-e-casella-privacy-prova-di-presa-visione.story.md
  - ./dcp-130-informativa-privacy-art13-e-pagina-servita.story.md
  - ./dcp-231-guardie-di-pubblicazione-di-terms-e-media-policy-e-feedback-accessibilita.story.md
  - ../compliance/privacy-policy-fonti-e-scelte.md
  - ../CreateTicketWizardWidget.md
  - ../../../../Themes/Sixteen/docs/stories/front-office-production-readiness-audit.story.md
  - ../../../../Themes/Sixteen/docs/stories/ticket-create-privacy-design-comuni-decision.story.md
issues: []
discussions:
  - "https://github.com/laraxot/theme_sixteen_fila5/discussions/96"
  - "https://github.com/laraxot/theme_sixteen_fila5/discussions/94"
---

# DCP-457: guardia, misure e decisioni aperte del primo livello dell'informativa nel passo 1

Status: **in corso** (2026-11-12). Fase BMAD: Analysis e Verifica (guardia) fatte; resta la misura nel browser.

## Perché esiste questa story e non una DCP-456 mia

Ho assegnato io stesso il numero 456 a questa story e, nello stesso quarto d'ora, un'altra sessione (non la 85) ha creato
[`ticket-create-privacy-avviso-breve.story.md`](./ticket-create-privacy-avviso-breve.story.md) con `id: dcp-456` per lo **stesso** problema, e ha anche scritto il
codice. La sua story è nata prima (file creato alle 07:45:06 UTC contro le 07:46:55 della mia prima scrittura), quindi il numero 456 resta a lei e questa prende 457.
Per non duplicare l'argomento questa story **non** descrive l'implementazione (è nella DCP-456): tiene l'analisi misurata, la guardia, la verifica e le decisioni
aperte che la DCP-456 non copre.

**Chi ha scritto il codice (solo ciò che posso verificare).** `components/gdpr-notice.blade.php` (07:49:23), `lang/{it,en}` (07:49:41), `lang/{de,es}` (07:49:47) e
`TicketForm.php` (07:50:22) sono stati modificati alle 07:49–07:50 UTC del 2026-11-12, non da me e non dalla sessione 85 (ha confermato di non aver scritto in nessuno di
quei file). La story DCP-456 descrive la propria implementazione ma non dice chi l'ha fatta. **Sessione terza, ID non accertato**: la sessione 85 ha verificato nel proprio
transcript completo di non aver scritto in quei file, io non li ho scritti, e a quell'ora risultavano attivi solo due processi `claude` (la 85 e questa). L'autore può
quindi essere una sessione già chiusa, un altro agente o l'altra macchina. Non scrivo nessun id. (Mio errore precedente: avevo scambiato per autrice una trascrizione che
conteneva le stesse chiavi solo perché le aveva lette; per attribuire serve una chiamata Edit/Write su quel file, non una semplice occorrenza della stringa.)
**Autore da accertare** con l'utente.

## Richiesta

Utente, 2026-11-12: «in /it/tickets/create (da loggato) c'e' troppo testo, come vedi segnalazione-01-privacy c'e' poco testo con links di approfondimento, devi sempre
capire il perche' obietti e senso, fa tutto con bmad + ecc + second brain».

## Perché c'era troppo testo (causa misurata, non ipotesi)

`TicketForm::getGdprHtml()` passava **tutto** `policy.md` a `gdpr-notice.blade.php`, togliendo solo il titolo. Quando DCP-46 e DCP-35 lo decisero, l'informativa aveva
«3 paragrafi, 8 righe». Con DCP-130 (art. 13 completo) è diventata un documento da 1.834 parole e il passo 1 è cresciuto da solo, senza che nessuna modifica lo riguardasse.
Il punto 10 di `compliance/privacy-policy-fonti-e-scelte.md` lo aveva già notato («valutare una informativa a livelli») ma nessuna story lo possedeva.

| Misura (prima) | Passo 1 | Riferimento `segnalazione-01-privacy` |
|---|---|---|
| Parole nel testo reso dal solo avviso (mia misura, `getGdprHtml()` sul codice originale) | **1.729**, 17 paragrafi, 10 titoli, 48 voci di elenco | tre righe |
| Parole nel `<main>` visibile a 1440 px (sessione 85, `innerText`) | **1.810**, 87 righe, 12 titoli, altezza pagina 6.351 px | **97**, un h1, una casella |
| Parole nel `<main>` visibile a 375 px (sessione 85) | **1.811**, altezza pagina 9.190 px | 98, altezza 2.901 px |
| Segnaposto `[[ente…` visibili al cittadino | **5** | 0 |

Effetto: la casella «Ho letto…» e «Avanti» stavano in fondo, dopo circa 6.000 px di informativa.

## Senso dell'intervento

**Informativa a due livelli.** L'art. 12 par. 1 GDPR chiede informazione concisa, trasparente, intelligibile e facilmente accessibile; le linee guida europee sulla
trasparenza raccomandano un primo livello breve al punto di raccolta e il testo completo raggiungibile. Copiare l'art. 13 integrale nel passo non aggiunge tutela: la
nasconde, perché nessuno lo legge prima di spuntare la casella. Vincolo che resta (`CreateTicketWizardWidget.md`): il testo legale è «contenuto di prima classe prima del
consenso», quindi il primo livello è un avviso vero e non un link nudo.

Alternative scartate (valide anche per la soluzione adottata dalla DCP-456): troncare `policy.md` (taglia a metà frase, dipende dal testo dell'ente); `<details>` chiuso
(il testo resta nel DOM, il peso non cambia e i segnaposto restano visibili); riassunto in testa a `policy.md` (il passo dipenderebbe da una frase che l'ente può togliere).

## Guardia

`Modules/Fixcity/tests/Unit/PrivacyStepIsATwoLayerNoticeTest.php`: prova sul **testo reso** da `getGdprHtml()`, con un'informativa finta di ~2.400 parole che contiene
`[[ente.name]]` (non dipende da ciò che l'ente ha scritto), per le quattro lingue servite:

- non più di 150 parole nel passo; nessuna riga dell'informativa; 0 segnaposto `[[ente`; 0 chiavi grezze `fixcity::`; nessun `<h1-6>` né `<li>`;
- un solo link a `/privacy`, nella lingua corrente;
- il nome dell'ente compare solo se `ente.name` è valorizzato, senza «:municipality» né «:name» residui;
- informativa non pubblicata: resta l'alert `role="alert"`;
- `ticket.gdpr_notice.text` e `text_no_name` esistono e sono diversi nelle quattro lingue (un fallback it che si nasconde farebbe due testi uguali).

## Verifica

Esito del 2026-11-12, dalla radice del monorepo con DB di prova (nessuna scrittura sul DB di sviluppo):

```
bashscripts/tools/pest-scratch-db.sh Modules/Fixcity/tests/Unit/PrivacyStepIsATwoLayerNoticeTest.php
```

12 test passati, 54 asserzioni. Vicini rimasti verdi: `CreateTicketWizardEnteGuardTest`, `WizardChromeLangTest`, `PrivacyConsentValidationMessageTest` (46 test, 180 asserzioni).
PHPStan: `[OK] No errors` su `TicketForm.php` e sul test; Pint passed sul test; `php -l` pulito sui file lang toccati.

**Prova al contrario: non pulita.** Il primo lancio del test ha dato 5 rossi su 11, ma in quel momento il codice stava cambiando (una chiave `text_no_name` in es non c'era
ancora): non misura la vecchia versione. Misurato prima, sul codice originale: 1.729 parole contro la soglia di 150. Non ho ripristinato il codice vecchio per rifare il
rosso (git solo in avanti, e i file sono di un'altra sessione).

## Misura nel browser: in lavorazione

**Misura in lavorazione, sessione 85, codice di sessione terza non ancora dichiarato pronto.** Browser reale, utente loggato, dev :8081, 2026-11-12 ~07:57 UTC. Riportata con il
consenso della sessione 85; la misura definitiva si rifà quando il codice è dichiarato pronto.

| Misura | Prima (sessione 85) | Ora | Riferimento |
|---|---|---|---|
| Parole nel `<main>` a 1440 px | 1.810 | **84** it, 88 en, 68 de, 92 es | 97 |
| Parole nel `<main>` a 375 px | 1.811 | **85** it, 89 en, 69 de, 93 es | 98 |
| Altezza pagina it a 1440 px | 6.351 px | 2.170 px | 1.938 px |
| Altezza pagina it a 375 px | 9.190 px | 2.302 px (en 2.283, de 2.306, es 2.312) | 2.901 px |
| h1, segnaposto `[[ente`, `fixcity::`, link a `/{lingua}/privacy` | 1 h1, 5 segnaposto | 1 h1 in 4 lingue × 2 larghezze, 0, 0, 1 link, status 200 | 1 h1 |

**Casella e «Avanti» nel primo schermo.**

- 1440 (viewport 900): casella a 769 px, «Avanti» 831–879. La barra cookie è in cima e spinge giù di circa 150 px; senza barra: ~620 e ~680.
- 375 (viewport 812): it casella 694, «Avanti» 729–777; en 644 e ~727. La barra cookie è fissa in basso (657–812) e li copre finché non si preme «Ho capito».
- 375, de: casella 823, «Avanti» 882–930; es: 825 e 882–932. **Sotto la piega anche senza la barra cookie.**
- Causa: non è il testo (85 parole) ma lo spazio sopra (intestazione, titolo, stepper: ~330 px) e il fatto che de ed es vanno a capo di più.

Quindi il criterio «senza scorrere» a 375 **non** è rispettato per de ed es, e per it ed en solo dopo aver chiuso la barra cookie. È una decisione aperta: accettare uno scroll
breve, oppure ridurre lo stepper a 375 (lavoro del tema Sixteen, non di questa story). La barra di debug in basso è solo dev e non conta.

**Difetto trovato:** il punto finale sta **dentro** il testo del link (`informativa sulla privacy.`) in tutte e 4 le lingue; andrebbe fuori dall'`<a>` (il punto sottolineato è
un'imperfezione e il testo del link letto da uno screen reader include la punteggiatura). Non lo tocco: la chiave `ticket.privacy.link.label` e la vista sono nel codice della sessione terza.

## Criteri di accettazione

- [x] ≤ 150 parole nel passo in it, en, de, es (riferimento: 98; soglia proposta dalla sessione 85).
- [x] 0 segnaposto `[[ente` e 0 chiavi grezze nel passo, in tutte e 4 le lingue.
- [x] Un solo link, a `/privacy` della lingua corrente.
- [x] Nome dell'ente solo se `ente.name` è valorizzato.
- [x] PHPStan 0 su `TicketForm.php` e sul test; Pint passed sul test.
- [x] Un solo h1 in it, en, de, es a 1440 e a 375 (misura in lavorazione, sessione 85).
- [ ] Casella «Ho letto…» e «Avanti» visibili senza scorrere: a 1440×900 sì (con la barra cookie in cima per un soffio, 879 su 900); a 375 **no** per de ed es, e per it ed en solo con la barra cookie chiusa. Da decidere (vedi sopra).
- [ ] Punto finale fuori dal link «informativa sulla privacy» (codice della sessione terza).
- [x] `TicketForm.php` formattato: gli `use` sono senza righe vuote doppie (controllato alle 07:5x UTC, file fermo dalle 07:50:22). Non ho lanciato io Pint su quel file.

## Decisioni aperte (non mie)

- **«sulla base dell'articolo 13 del Regolamento»**: è la copy del riferimento, ma l'art. 13 è l'articolo dell'informativa, non una base giuridica (quelle sono nell'art. 6).
  Non la cambio: dipende da DCP-134 (`needs-decision`, presa visione contro consenso) e il DPO dell'ente approva il testo.
- **Link di approfondimento a sezione di `/privacy`** (Finalità, Diritti, Conservazione): oggi 0 titoli su 11 hanno un `id` (misura sessione 85); servono id **stabili**, non
  derivati dal titolo tradotto, e il renderer è del modulo Gdpr.
- **Link a condizioni d'uso e media policy dal passo**: dopo DCP-231 (non hanno guardie di pubblicazione, possono mostrare «Testo non ancora completo»).
- **Nome del Comune**: dipende da `ente.php`, oggi vuoto: finché lo è, il cittadino legge «Il Comune gestisce…» senza nome (DCP-208).
- **Segnaposto `[[ente.*]]` e alert «Testo non ancora completo» di `/it/privacy`**: dati non compilati dall'ente, tema di messa online (DCP-208, DCP-231), non toccati.
- **Numero e date della DCP-456**: la sua story riporta `created`/`updated` 2026-10-14 e la richiesta datata 2026-10-14; il file è stato creato oggi (2026-11-12) e la discussion
  collegata è la #69 del repo del modulo, non la #94 dell'audit. Da verificare con chi l'ha scritta.

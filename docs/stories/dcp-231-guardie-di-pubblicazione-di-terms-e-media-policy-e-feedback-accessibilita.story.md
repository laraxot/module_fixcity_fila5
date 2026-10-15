---
id: dcp-231
title: "DCP-231: guardie di pubblicazione mancanti per terms e media-policy, e modulo di feedback sull'accessibilita' spento"
type: story
epic: dc-parity-v2
status: open
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "guardie pubblicazione publication-gate terms media-policy assess modulo feedback accessibilita feedback_email meta description promette privacy informativa al form note legali condizioni"
related: [dcp-186, dcp-200, dcp-154, dcp-130, dcp-132, dcp-162, dcp-203]
tags: [legale, go-live, bmad, front-office]
---

# DCP-231: guardie di pubblicazione e feedback sull'accessibilita'

## Fase BMAD
Plan. Dal gruppo D dell'audit [DCP-186](../../../../Themes/Sixteen/docs/bmad/stories/dcp-186-front-office-scopo-e-senso-di-ogni-pagina-ed-elemento.story.md) (voci D9 e D13, scheda in `Themes/Sixteen/docs/compliance/03-front-office-scheda-per-pagina.md`). I testi e le basi giuridiche (D1-D5, D14) sono dell'ente, del titolare e del DPO e stanno nel registro [DCP-200](./dcp-200-registro-unico-delle-decisioni-aperte.story.md); qui c'e' solo cio' che e' di competenza dello sviluppo.

## Scopo
Un testo legale incompleto non deve mai arrivare in produzione senza che nulla se ne accorga. Per `privacy`, `note-legali` e `accessibilita` esistono guardie rosse per costruzione (`PUBLICATION_GATE=1 pest --group=publication-gate`). Per `terms` e `media-policy` no.

## Difetti (MISURATI dall'agente del gruppo D, riletti a campione dal coordinatore)
- **D9.** Per `terms` e `media-policy` il JSON di pagina non ha `assess` e non esiste un modello in `resources/templates`: quando l'ente scrive un testo qualunque, anche pieno di segnaposto, la pagina passa a 200 **senza avviso e senza controllo**. Oggi mostrano solo «Testo non ancora completo» (12 pagine su 20 con avviso, gate di messa online `LEG-04`).
- **D13.** Il meccanismo di feedback sull'accessibilita' (L. 4/2004 art. 3-quater c. 2) e' spento: `feedback_email` e' nullo in `accessibilita.index.json`; la pagina dice «usa il modulo qui sotto» e subito dopo «sara' attivo quando...», e la meta description lo promette. Il form raccoglie testo libero ed email facoltativa senza rimando all'informativa (DEDOTTO da `feedback-form.blade.php`). Limite 5 invii all'ora per IP con `RateLimiter`: **non provato** quale store di cache lo regge; la consegna dell'email non e' provata.
- **D11, D12 etichette e avvisi.** Il titolo della pagina e l'etichetta nel footer non coincidono (en «Privacy policy» contro «Privacy notice», es «Politica de privacidad» contro «Aviso de privacidad», es «Aviso legal» contro «Notas legales», de «Medienrichtlinie» contro «Media Policy»: WCAG 3.2.4, informativo). In de ed es l'avviso di terms e media-policy omette «prima della messa in produzione».

## Cose da fare
- [ ] Modello (`resources/templates/terms.it.md` e `media-policy.it.md`) con le sezioni obbligatorie e `assess` nei JSON, come per `legal-notes` (`AssessPrivacyPolicyAction`, `AssessLegalNotesAction`, `AssessAccessibilityStatementAction`): un'`Action` per ciascuna.
- [ ] Guardie `PUBLICATION_GATE` per `terms` e `media-policy` (rosse per costruzione finche' mancano le sezioni): `TermsPublicationGateTest`, `MediaPolicyPublicationGateTest`, nel gruppo `publication-gate`, e voce nel gate di messa online (`LEG-01`) con il numero di guardie attese (oggi 3, diventano 5).
- [ ] Nessun avviso di uso interno visibile al cittadino in produzione: l'avviso compare solo se `APP_ENV` non e' produzione o se il testo manca, e in tutte e 4 le lingue dice la stessa cosa (chiave unica in `legal.php`).
- [ ] Modulo di feedback: se `feedback_email` e' nullo la pagina **non promette il modulo** (ne' nel testo ne' nella meta description); con la casella impostata il form ha il rimando all'informativa e la richiesta di consenso minima; prova dell'invio con una casella di prova (`MAIL_MAILER=array`) e del limite con lo store di cache di produzione.
- [ ] Etichette del footer uguali al titolo della pagina, nelle 4 lingue (un'unica chiave per pagina).
- [ ] Aggiornare la matrice di interfaccia (I-01, I-21) e la scheda per pagina.

## Decisioni da prendere
| ID | Domanda | Opzioni | Proposta | Chi decide | Blocca |
|---|---|---|---|---|---|
| D231-1 | `terms` e `note-legali` restano due pagine? | due; una sola «Termini e note legali» | una sola (DEC-17 del registro): se si fonde, `terms` diventa un reindirizzamento 301 e la guardia e' una | prodotto, poi ente | modelli e guardie |
| D231-2 | `media-policy`: serve? | si' se l'ente ha canali social; no | no finche' l'ente non fornisce gli account (DEC-03): togliere la pagina dal footer e dalla sitemap | ente | guardia di media-policy |
| D231-3 | Casella del feedback sull'accessibilita' | indirizzo dell'ente (RTD); casella del fornitore | indirizzo dell'ente (DEC-04), distinto da quello di sicurezza | ente | D13 |
| D231-4 | Informativa al punto di raccolta del feedback | link a `/privacy`; informativa breve dedicata | link a `/privacy` piu' una riga sul trattamento del testo libero, approvata dal DPO | DPO | D13 |

## Criteri di accettazione
- [ ] `PUBLICATION_GATE=1 pest --group=publication-gate` ha 5 guardie, tutte rosse finche' mancano i dati dell'ente e verdi con dati di prova; senza la variabile risultano saltate e il gate di messa online le conta come **fallite**.
- [ ] Nessuna promessa di un modulo che non c'e'; con la casella impostata, l'invio di prova arriva e il limite scatta alla sesta richiesta.
- [ ] `go-live-gate.py` rimisurato (`LEG-01`, `LEG-04`, `LG-02`) e l'esito scritto in DCP-154.

## Non verificato
Parere del DPO e di un legale (non e' dello sviluppo); consegna reale dell'email; store di cache e limite in produzione; lettore di schermo sulla pagina di accessibilita'.

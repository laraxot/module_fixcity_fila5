---
id: dcp-162
title: "DCP-162: note legali, disclaimer e condizioni di utilizzo, modello in quattro lingue con verifica"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "note legali disclaimer condizioni di utilizzo termini limitazione responsabilita contenuti utenti legal-notes legal_notes AssessPrivacyPolicyAction modello segnaposto 6.12 6.16 emergenza 112"
related: [dcp-130, dcp-131, dcp-135, dcp-154, 6.12, 6.16]
---

# DCP-162: note legali, disclaimer e condizioni di utilizzo, modello in quattro lingue con verifica

## Lacuna e prova (2026-10-14)
`/{it,en,de,es}/note-legali` risponde 200 ma mostra solo l'avviso «testo non ancora completo»: manca `legal-notes.md` del tenant (il `terms.md` presente e' il segnaposto del framework, 88 byte, «Edit this file»). Non esiste alcun testo su: che il servizio non e' un canale di emergenza, regole d'uso, responsabilita' per i contenuti inviati dai cittadini e loro pubblicazione, limitazione di responsabilita', link a terzi, licenza e riuso, legge applicabile, modifiche, contatti. Fonti studiate: guida di sos-wp.it (2018, rev. 2022: informativa privacy come obbligo, disclaimer «strumento comune», cita il provvedimento Garante 2014 e l'art. 13 D.Lgs. 196/2003, entrambi superati dal GDPR e dalle Linee guida cookie 2021), termini di utilizzo di Servizi Industria S.r.l. (14 sezioni: responsabilita', copyright, link, restrizioni, servizi di comunicazione, privacy; **mancano** modifiche ai termini, legge applicabile e foro, contatti, e ha dati societari incoerenti). TermsFeed: 403 al nostro accesso, non letto.

## Norma
Non esiste un obbligo di legge di avere un «disclaimer»: le fonti commerciali lo presentano come strumento, non come obbligo; obbligatorie sono l'informativa privacy (GDPR artt. 12-13) e l'informazione sui cookie. Per un sito di una PA le note legali sono previste dal modello Design Comuni (voce del footer) e dal requisito 4.2.5 delle Linee guida di design (informativa) e 4.7 (licenza). Il contenuto giuridico lo decide l'ente: matrice N-30, I-15.

## Requisiti
1. Estendere il meccanismo gia' usato per l'informativa: `AssessPrivacyPolicyAction` verifica anche le 11 sezioni del modello (`NOTES_SECTIONS`: titolare, finalita', condizioni d'uso, contenuti degli utenti, responsabilita', link di terzi, proprieta' intellettuale, dati e cookie, modifiche, legge applicabile, contatti); `BuildLegalDocumentAction` accetta `assess: legal-notes`; i JSON delle pagine `note-legali` dei tenant `local`, `eu` e del modulo lo dichiarano.
2. Modello da compilare in `Fixcity/resources/templates/legal-notes.{it,en,de,es}.md` con `[[segnaposto]]` per ogni dato dell'ente e titoli identici al file lingua `lang/<lingua>/legal_notes.php` (con suggerimento per ogni sezione).
3. Un modello non compilato **non e' pubblicabile**: le 8 sezioni che richiedono dati dell'ente (titolare, finalita', contenuti degli utenti, responsabilita', proprieta' intellettuale, modifiche, legge applicabile, contatti) risultano `placeholder`; le altre 3 (condizioni d'uso, link di terzi, rimandi a privacy, cookie e accessibilita') sono testo di modello che il legale deve comunque rivedere. La nota in testa al modello e' fra `[[ ]]`: finche' non viene tolta il documento resta non pubblicabile.
4. Guardia di pubblicazione rossa per costruzione (`PUBLICATION_GATE=1`) e test del modello nelle 4 lingue.
5. Decisione: le condizioni d'uso stanno nelle note legali (voce gia' nel footer, come nel modello Design Comuni) e non in una pagina separata. Le story 6.12 e 6.16 chiedevano un documento «termini e disclaimer» distinto: l'utente decide se separare.

## Criteri di accettazione
- [x] `AssessPrivacyPolicyAction` con documento `legal_notes`; JSON dei 3 posti aggiornati; blocco del tema mostra titoli e suggerimenti delle note legali.
- [x] Modello e file lingua in 4 lingue, stesse 11 chiavi.
- [ ] Test unitari: modello non compilato = le 8 sezioni con dati dell'ente in `placeholder`, nota in testa che blocca la pubblicazione, modello compilato e senza nota = pubblicabile; testo completo = nessun problema; sezione mancante o vuota segnalata; titoli del modello = titoli del file lingua in it, en, de, es.
- [ ] Test di pagina: `/it/note-legali` mostra l'avviso con i titoli delle note legali (non dell'informativa) e, con un testo completo, il testo e nessun avviso.
- [ ] `PUBLICATION_GATE=1 pest --group=publication-gate` fallisce finche' l'ente non fornisce il testo (come per l'informativa).
- [ ] Revisione del legale e del DPO dell'ente; l'ente fornisce identita', licenza, foro e contatti (DCP-133).

## Dipendenze e priorita'
DCP-133 (identita' dell'ente), DCP-154 (il gate controlla anche questa guardia), DCP-170 (licenza), DCP-164 (DPO). P0: senza testo il servizio non e' pubblicabile.

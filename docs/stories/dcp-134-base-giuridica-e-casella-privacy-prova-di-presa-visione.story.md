---
id: dcp-134
title: "DCP-134: base giuridica e casella privacy: consenso o presa visione, con prova"
type: story
epic: dc-parity-v2
status: needs-decision
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "base giuridica consenso presa visione art 6 art 7 considerando 43 autorita pubblica checkbox privacyAccepted prova versione informativa"
related: [dcp-112, dcp-130, dcp-135]
---

# DCP-134: la casella del passo 1 dice la cosa giusta e lascia una prova

Nata da DCP-112 (matrice normativa N-01, N-02).

## Lacuna e prova (2026-10-13)
- L'informativa pubblicata chiude con «Proseguendo, dichiari di aver letto questa informativa e acconsenti al trattamento dei dati» (`config/local/fixcity/lang/it/policy.md`); la casella del passo 1 e' obbligatoria (`TicketForm::getPrivacySchema`).
- `privacyAccepted` e' validato ma non e' persistito (DCP-49 SEC-12): nessuna prova di chi ha preso visione, quando e di quale versione.
- Il PRD (`bmad-output/prd.md` NFR-007, `security-plan.md`) rimanda la scelta al titolare: «The checkbox may be acknowledgement or legal consent; code must not decide».

## Norma (testo letto, registro fonti 02)
GDPR art. 6 par. 1 lett. e) (compito di interesse pubblico) e ultimo comma del par. 1 (la lett. f) non vale per le autorita' pubbliche nell'esecuzione dei loro compiti); art. 7 (onere di dimostrare il consenso); Considerando 43: il consenso non e' valido presupposto «qualora esista un evidente squilibrio [...] specie quando il titolare del trattamento e' un'autorita' pubblica»; D.Lgs. 196/2003 art. 2-ter c. 1 e 1-bis.

## Decisione richiesta al titolare (DPO)
A) Base = compito di interesse pubblico: la casella diventa presa visione dell'informativa (testo "ho letto", non "acconsento") oppure sparisce a favore di un avviso; nessun consenso per il servizio.
B) Base = consenso: motivare per iscritto perche' non c'e' squilibrio e come si revoca (art. 7 par. 3).
La decisione va scritta nel decision log del tema (prossimo numero D libero) con data e firmatario.

## Requisiti (dopo la decisione)
1. Testi in it, en, de, es coerenti con la scelta (`policy.md`, `ticket_form`, `ticket` lang).
2. Prova: al salvataggio del ticket si registrano `acknowledged_at` e un identificativo della versione dell'informativa (hash del file `policy.md` della lingua). Servono nuove colonne: per regola di progetto vanno approvate prima (migrazione forward-only, ask-first).
3. Il passo 1 resta bloccato quando manca un'informativa pubblicata (comportamento attuale, da mantenere).

## Criteri di accettazione
- [ ] Decisione registrata e firmata (file nel decision log) prima di toccare il codice.
- [ ] Test: creare un ticket salva `acknowledged_at` e la versione; con informativa modificata la versione cambia.
- [ ] `grep -rn 'acconsent' laravel/config/local/fixcity/lang/*/policy.md` coerente con A) o B).
- [ ] Il test esistente del blocco del consenso (8 casi, DCP-49) resta verde.

## Dipendenze e priorita'
Decisione del DPO; DCP-130 (testo), DCP-135 (registrazione). P0 per la decisione, P1 per l'implementazione.

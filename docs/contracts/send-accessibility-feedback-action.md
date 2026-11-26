---
title: "Contratto — SendAccessibilityFeedbackAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SendAccessibilityFeedbackAction"
module: Fixcity
status: accepted
tags: [contract, action, email, accessibilita, privacy, throttle]
created: 2026-11-26
updated: 2026-11-26
---

# SendAccessibilityFeedbackAction

Sorgente: `app/Actions/SendAccessibilityFeedbackAction.php` (DCP-132; L. 4/2004 art. 3-quater c. 2 lett. b). Spedisce alla casella dell'ente la segnalazione di un difetto di accessibilita'
scritta da un visitatore anonimo. Unico chiamante: `Livewire/AccessibilityFeedbackForm.php`, che convalida l'input, sceglie il destinatario e applica il freno. L'Action e' volutamente sottile: le
garanzie di sicurezza stanno nel chiamante, e questo contratto le registra perche' chi riusa l'Action le erediti.

## Input

- `string $recipient`: scelto dal chiamante, leggendo `declaration.feedback_email` lato server (`GetAccessibilityDeclarationAction`). **Mai** preso da dati del visitatore.
- `string $problem`: descrizione del visitatore.
- `string $page = ''`: pagina interessata.
- `string $contact = ''`: email di contatto del visitatore, facoltativa; diventa il `Reply-To`.

## Output

- `void`.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| A11Y-FEEDBACK-INV-1 | Il destinatario non e' una proprieta' del componente Livewire: il client non puo' impostarlo (nessuna proprieta' `recipient`; il tentativo di scriverla lancia). La mail va alla casella dichiarata dall'ente. | `A11Y-FEEDBACK-INV-1` |
| A11Y-FEEDBACK-INV-2 | Senza una casella dichiarata, o con una casella non valida, il chiamante non invoca l'Action: nessuna mail e esito `unavailable`. | `A11Y-FEEDBACK-INV-2` |
| A11Y-FEEDBACK-INV-3 | L'input e' convalidato prima dell'invio: un `problem` troppo corto e un `contact` che non e' un'email danno errori sui due campi e non parte nessuna mail. | `A11Y-FEEDBACK-INV-3` |
| A11Y-FEEDBACK-INV-7 | I limiti di lunghezza sono `problem` da 10 a 3000 caratteri, `page` fino a 255, `contact` fino a 255. | — (non dimostrato: il test prova solo il limite inferiore di `problem` e il formato di `contact`) |
| A11Y-FEEDBACK-INV-4 | Al massimo `MAX_PER_HOUR` (5) segnalazioni all'ora dallo stesso indirizzo: la sesta ha esito `throttled` e non invia (`RateLimiter::attempt`, chiave `accessibility-feedback:{ip}`, 3600 s: il test non distingue indirizzi diversi). | `A11Y-FEEDBACK-INV-4` |
| A11Y-FEEDBACK-INV-5 | La mail parte verso la casella dell'ente con il contatto del visitatore, se c'e', come `Reply-To`, e con problema e pagina nel corpo; dopo l'invio il modulo si svuota ed esce `sent`. | `A11Y-FEEDBACK-INV-5` |
| A11Y-FEEDBACK-INV-6 | Il corpo e' testo semplice (`Content(text: ...)` in `AccessibilityFeedbackMail`), mai HTML: quello che scrive il visitatore non diventa markup. | — (non dimostrato) |

## Effetti collaterali

- `Mail::to($recipient)->send(AccessibilityFeedbackMail)`: un solo messaggio, **sincrono** nella richiesta Livewire (la mail non e' in coda).
- Nessuna scrittura su database, nessun log, nessuna traccia della segnalazione oltre alla mail: se la casella non la riceve, il testo e' perso.

## Errori

- Un errore del trasporto propaga `Throwable` dall'Action: il chiamante non lo cattura e l'utente vede un errore del componente Livewire, senza esito `sent`. Il tentativo ha comunque consumato un
  posto del freno, perche' `RateLimiter::attempt` incrementa prima di eseguire la closure.
- Limite noto: l'Action non convalida `$recipient`, `$problem` ne' `$contact`: la protezione e' tutta nel chiamante (INV-2, INV-3). Un chiamante nuovo che la usa senza passare dal modulo non e' protetto.
- Limite noto: il freno e' per IP (`request()->ip()`): dietro un NAT condiviso i visitatori si dividono le 5 segnalazioni orarie.
- Limite noto: il testo del `problem` e della `page` entra nel corpo senza filtri di lunghezza dentro l'Action (la lunghezza e' del form).

## Non fa

- Non registra la segnalazione nel database ne' le assegna un numero o una risposta al visitatore.
- Non conferma la ricezione da parte dell'ente: `sent` significa solo che il trasporto ha accettato il messaggio.
- Non sceglie il destinatario e non legge la dichiarazione di accessibilita'.
- Non e' collegata a un ticket: non passa dal flusso delle segnalazioni dei cittadini.

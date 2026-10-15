---
title: "BMAD 08 — Security e privacy FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, security, privacy, policy, fixcity]
module: Fixcity
qmd: "bmad security privacy ticketpolicy isolation responsible_id fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-citizen.md
  - actor-operator.md
  - 06-quality-assurance.md
  - ../stories/STORY-026-rbac-operator-supervisor.md
---

# Security

**Perché:** una segnalazione contiene dati personali e posizione; l'isolamento
tra cittadini e i ruoli PA sono requisiti di fiducia pubblica.

## Controlli obbligatori

1. Auth cittadino e sessione; mostrare e accettare il consenso solo se il tenant
   ha pubblicato la propria policy localizzata. Se è assente, pagina privacy 503,
   checkbox disabilitata e submit FO rifiutato lato server (G-30).
2. `TicketPolicy`: create/view/update/delete/assign/changeStatus/changePriority — argomenti `UserContract`.
3. Cittadino A non legge/modifica ticket di cittadino B.
4. Operatore non supera perimetro; supervisor/admin restano auditati.
5. Mass assignment: solo fillable previsti; upload validati.
6. Posizione e allegati: minimizzazione e retention coerenti con privacy.
7. Log senza segreti; notifiche senza leak di dati altrui.
8. Transizioni fuori matrice `allowedTransitions()` rifiutate.
9. Il codice di tracking è una capability bearer: il proprietario e i follower
   autorizzati di un ticket pubblico lo ricevono nel link alla timeline pubblica.
   Mai includere l'ID sequenziale nei link pubblici/notifiche ai cittadini; la
   notifica interna all'assignee usa invece l'ID dietro autenticazione e Gate.

## Enforcement delle azioni

- Ogni azione Filament record-specific deve dichiarare `authorize('<ability>')`;
  il record corrente viene passato alla policy da Filament.
- Le Actions invocabili da più ingressi applicano `Gate::authorize()` al confine
  applicativo (assegnazione, eliminazione e priorità). Non affidarsi alla sola
  visibilità della UI.
- Il cambio stato automatico alla creazione è un percorso di sistema e passa da
  `TicketCreatedListener`; l'azione Filament `ChangeStatus` resta protetta da
  `changeStatus`.
- Non importare il model concreto User per ruoli o permessi. Le policy ricevono
  `UserContract`; i selettori user interrogano la classe restituita da
  `XotData::make()->getUserClass()`.
- La lista “Segnalazioni seguite” e le notifiche di transizioni pubbliche usano
  `/tickets/track?code=<capability>`: il destinatario ha effettuato opt-in al
  follow e la transizione è pubblica. Non inviare codice o timeline a follower
  per attività interne/non pubbliche. Per il dettaglio privato del proprietario,
  l'area personale usa `ticket_id` e la pagina applica `Gate::authorize('view', …)`;
  l'ID non sostituisce l'autorizzazione. La notifica di assegnazione PA rimanda
  alla resource autenticata e non condivide il link bearer.
- Coprire allow/deny con test di policy senza DB quando la logica non richiede
  persistenza. La matrice runtime Filament resta da verificare sul database di test.

## Gate

Test negativi per ogni confine; audit/timeline coerenti con l'attore.
La matrice pura copre operator/supervisor/admin/citizen, permesso singolo,
visibilità pubblica e ownership contro i soli campi audit in
`tests/Unit/Policies/TicketPolicyTest.php`. Gli scenari Feature e l'autorizzazione
Livewire/Filament richiedono ancora il grant di `fixcity_data_test` per MySQL.
`TicketStatusNotificationTest` copre il code-capability a owner/follower solo per
stati pubblici e non per eventi interni; `TicketAssignmentNotificationTest` copre
il solo assignee PA. I test di privacy verificano il blocco quando manca la policy.

## Output

Checklist security + test policy/feature.

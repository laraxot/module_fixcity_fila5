---
title: "FixCity privacy acknowledgement semantics and audit"
type: story
status: blocked
module: Fixcity
actor: Citizen / Data controller
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, privacy, consent, audit, gdpr]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - STORY-FIXCITY-PRIVACY-PUBLICATION-GATE.md
  - ../gap-analysis.md
  - ../workflows/actor-citizen.md
  - ../../../../Gdpr/docs/user-registration-integration.md
---

# Problema

Il wizard mostra un checkbox “Ho letto e compreso l'informativa”. Il flag
`privacyAccepted` è validato come requisito UI, ma `CreateTicketWizardWidget::save()`
non lo passa come evidenza a una registrazione persistente e `CreateTicketAction`
crea solo il ticket. Il dato non è quindi dimostrabile consultando il record.

Il codice da solo non può decidere se il controllo esprima una presa visione o un
consenso al trattamento, quale evidenza sia necessaria, né retention e accessi. Non
attribuire significato giuridico al checkbox né registrare IP/user-agent senza la
decisione approvata dal titolare.

# Acceptance criteria

- [ ] Il titolare/privacy owner approva per ogni lingua se il controllo è presa visione,
      consenso o informazione senza checkbox e quali basi/purpose descrive la policy.
- [ ] Product + privacy owner decidono se l'evento deve essere conservato, accessibile
      a chi, per quanto tempo e con quale prova della versione pubblicata.
- [ ] La soluzione tecnica segue la decisione approvata: se serve audit, salva in modo
      server-side e atomico con il Ticket il riferimento non modificabile alla versione
      della policy, locale, istante e attore; non fidarsi di testo/versione forniti dal client.
- [ ] Se il checkbox non è richiesto, il flusso FO non blocca l'invio su un requisito
      privo di approvazione e la UI spiega correttamente la sola informativa.
- [ ] Pest copre entrambi gli esiti approvati, falsificazione input, atomicità e accesso
      all'evidenza; PHPStan, UI responsive e accessibilità sono verdi.

# Dipendenza

Decisione umana del titolare e product owner. Fino a quel momento il codice mantiene
il fail-closed della [publication gate](STORY-FIXCITY-PRIVACY-PUBLICATION-GATE.md):
non si accettano ticket quando la policy è assente o template.

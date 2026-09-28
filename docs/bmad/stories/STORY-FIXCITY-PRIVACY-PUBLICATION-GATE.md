---
title: "FixCity tenant privacy publication gate"
type: story
status: in-progress
module: Fixcity
actor: Citizen / Tenant administrator
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, privacy, gdpr, tenant-configuration, security]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../gap-analysis.md
  - ../workflows/actor-citizen.md
  - ../workflows/08-security.md
  - ../../../../../Themes/Sixteen/docs/architecture/privacy-policy-folio-route.md
  - STORY-FIXCITY-PRIVACY-ACKNOWLEDGEMENT-AUDIT.md
---

# Problema

La pagina informativa tenant era solo un segnaposto, ma il wizard mostrava copy
legale che attribuiva il trattamento al Comune di Firenze, non configurato per
questo tenant. La checkbox consentiva l'invio anche senza un'informativa leggibile.

# Criteri di accettazione

- [x] La policy è risolta dal tenant corrente e dalla lingua attiva tramite
      `GetLocalizedMarkdownPathAction`.
- [x] Un file vuoto o ancora segnaposto non è considerato pubblicato.
- [x] La pagina Folio `/{locale}/privacy` del tema Sixteen precede il catch-all
      CMS e rende Markdown tenant solo se presente; altrimenti risponde 503.
- [x] Il wizard disabilita il consenso e rifiuta il submit server-side se manca
      una policy; anche il widget legacy non può aggirare il gate.
- [x] Rimossi i riferimenti runtime inventati al Comune di Firenze.
- [x] Regressioni privacy (3 test), blocco consenso (8 test), enum UI (9 test): 20 test / 68 asserzioni mirate passano su SQLite isolato.
- [x] Browser privacy IT/EN a 320/390/1440 px: 6/6 richieste HTTP 200, nessun overflow o errore JS; copy localizzato.
- [ ] Suite Fixcity completa verde: l'ultimo run completo (prima delle ultime correzioni) riportava 20 fallimenti e 375 passaggi; non ancora rilanciato.
- [ ] Il proprietario dell'ente deve pubblicare e approvare il testo ufficiale
      in `config/<tenant>/lang/{locale}/policy.md`; la policy attuale è contenuto demo, non approvazione legale.
- [ ] Chiarire la semantica e la prova del checkbox, secondo la story [privacy acknowledgement](STORY-FIXCITY-PRIVACY-ACKNOWLEDGEMENT-AUDIT.md).

# Dipendenza umana

Nessun titolare, finalità, base giuridica, retention, contatto DPO o testo legale
viene inventato dal codice. Senza il Markdown ufficiale del tenant il servizio
segnalazioni rimane intenzionalmente non disponibile: è un blocco di go-live.

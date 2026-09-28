---
title: "FixCity — piano correttivo UI/UX guest"
type: bmad-implementation-plan
status: in_progress
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, ui, ux, dev-command, browser]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
---

# Piano correttivo

1. [ ] Stabilizzare `php artisan dev`: nel run verificato Vite è terminato su
   `Modules/Seo/.agents/skills/qmd` (`ELOOP`); il runner `logs` invoca `pail`,
   che non è registrato. Il manifest compilato serve correttamente gli asset.
2. [ ] Decidere e documentare un artefatto browser riproducibile sotto
   `storage/app/demo-audit`; gli screenshot di questa sessione sono temporanei
   e restano fuori dal repository.
3. [x] Auditare guest `/it` e `/en` con Chromium a 320/390/768/1024/1440 px:
   asset, errori, overflow, lingua e destinazioni CTA. Evidenze nel confronto.
4. [x] Correggere i difetti riprodotti: contrasto/padding CTA della hero,
   brand framework visibile e breadcrumb con chiave di traduzione grezza.
5. [x] Rendere `/tickets` la lista pubblica canonica e mantenere `/segnalazioni`
   come alias legacy localizzato.
6. [x] Allineare il popup marker: label tradotta, destinazione valida, contrasto
   protetto e contenuto compatto sui viewport mobili e tablet.
7. [x] Studiare i sette prototipi ufficiali Design Comuni e aggiornare il
   contratto BMAD con i passaggi e i requisiti riscontrati.
8. [x] Aggiornare confronto, story e Second Brain con esiti verificati;
   la story resta aperta per l'avvio demo HMR e l'artefatto persistente.

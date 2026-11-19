---
type: bmad-initiative
status: completed
updated: 2026-10-17
initiative_id: kp-faq-route-fix
tags: [faq, route-not-found, design-comuni, bmad, second-brain, filament-blueprint]
summary: |
  Errore RouteNotFoundException: Route [pages.view] not defined su /it/domande-frequenti. Causa: faq.blade.php (line 52) usava route('pages.view', ['slug'=>'accessibility']) che non esiste nel progetto. Correzione: sostituito con URL diretto /accessibility (hardcoded, allineato con design-comuni). Cache view pulita (view:clear). Verificato localmente (200 OK). Server remoto (fixcity.eu) richiede deploy della view aggiornata.
files_changed:
  - laravel/Themes/Sixteen/resources/views/components/pages/faq.blade.php
  - laravel/Modules/Comment/docs/bmad/faq-parity.md (riferimento)

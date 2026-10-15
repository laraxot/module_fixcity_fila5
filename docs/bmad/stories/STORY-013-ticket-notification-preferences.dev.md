---
title: "STORY-013 — Piano di sviluppo"
type: implementation-plan
status: implemented
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, notification, preferences]
module: Fixcity
qmd: "Fixcity notification preferences implementation plan"
issues:
  - https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
  - https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

# Piano di sviluppo

1. Riutilizzare `profiles.preferences` esistente; persistere sotto la chiave
   `fixcity.email_ticket_updates`, conservando ogni altra preferenza.
2. Creare Action Fixcity `Get...` e `Set...` che lavorano con `UserContract` e
   la relazione `profile()` configurata dal modulo User.
3. Montare un widget Livewire nel percorso Sixteen `area-personale.impostazioni`;
   non introdurre controller, Services, tabelle o dipendenze tema→Fixcity.
4. Fare dipendere i canali mail delle notifiche ticket dalla preferenza, oltre
   alla verifica email. Il canale database resta sempre attivo.
5. Coprire guest/auth, default, toggle, chiavi estranee, stato non verificato,
   rendering della pagina e canali notifiche con Pest.
6. Validare PHPStan `analyse Modules`, Pint, suite Fixcity e quality gate wiki.

## Esito implementazione

- Actions e campo `preferences.fixcity.email_ticket_updates` implementati senza migration;
  le preferenze estranee restano intatte.
- Widget Livewire montato via classe sul percorso `area-personale.impostazioni`;
  link alle impostazioni visibile dalla navigazione cittadino.
- Gli aggiornamenti stato e assegnazione tengono sempre il canale database e
  abilitano email solo per indirizzi verificati/non vuoti con preferenza attiva.
- Pest mirato: **12 test / 37 asserzioni passati**; suite Fixcity: **356 test / 1.458
  asserzioni passati**; PHPStan `analyse Modules` senza errori. Pint mirato e
  `php artisan view:cache` passati.
- `pint --test` globale non passa per numerosi file legacy non formattati e parse
  error in `Themes/Sixteen/src/Models/Municipal/MunicipalEvent.php` e doppioni sotto
  `Themes/Sixteen/Sixteen`; nessuno è stato alterato in questa story.
- Nessuno screenshot browser allegato: Chromium non è disponibile nell'ambiente
  di esecuzione; test HTTP e Livewire verificano accesso, markup e salvataggio.

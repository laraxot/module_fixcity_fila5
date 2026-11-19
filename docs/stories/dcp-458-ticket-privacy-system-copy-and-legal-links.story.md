---
id: DCP-458
title: Ticket wizard privacy: sistema FixCity e approfondimenti legali
type: story
status: implemented-pending-browser-proof
owner: Fixcity
related_discussion: https://github.com/laraxot/theme_sixteen_fila5/discussions/96
related_issue: https://github.com/laraxot/theme_sixteen_fila5/issues/98
---

# Decisione

Lo step `Autorizzazioni e condizioni` appartiene al sistema FixCity. Il copy non deve
attribuire il trattamento a un Comune configurato né dedurre un titolare dalla tenant
configuration: in questo punto serve spiegare in modo breve cosa fa il sistema con i
dati inseriti. Il consenso rinvia alla pagina completa, senza duplicare l'informativa.

# Acceptance criteria

- Il testo italiano dice che il sistema FixCity riceve segnalazioni di disservizi,
  le gestisce e comunica gli aggiornamenti/esito.
- Nessun testo dello step usa `Comune`, `municipality`, `ayuntamiento` o placeholder
  `:entity` per descrivere il soggetto.
- Sono presenti quattro link localizzati: privacy, termini, note legali e cookie policy.
- Il fallback senza policy resta fail-closed e mantiene l'alert accessibile.
- Il markup resta breve, senza titoli o copia dell'informativa completa.

# Implementazione e verifica

`TicketForm::getGdprHtml()` costruisce il contratto `legalLinks` con
`LaravelLocalization::getLocalizedURL()`; il componente Blade rende il primo link nel
periodo esplicativo e gli altri tre in una riga di approfondimento. Le pagine CMS locali
sono già presenti in `config/local/fixcity/database/content/pages/`.

Gate minimo:

```bash
cd laravel
./vendor/bin/pest Modules/Fixcity/tests/Unit/PrivacyStepIsATwoLayerNoticeTest.php --no-coverage
php artisan view:cache
```

Restano da provare con browser autenticato i viewport 375, 768 e 1440; il test unitario
non dimostra da solo il rendering HTTP del tenant.

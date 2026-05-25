---
title: xotbasewidget — niente $view esplicito sul widget figlio
type: concept
confidence: high
created: 2026-05-23
updated: 2026-05-23
tags: [filament, xot-basewidget, wizard, laravel-livewire, fixcity]
related:
    - ../../../docs/ticket-wizard-frontoffice.md
    - ../../../../../Modules/Xot/app/Filament/Widgets/XotBaseWidget.php
    - ../../../../../Modules/Xot/app/Actions/View/GetViewByClassAction.php
---

# perché

Estendendo **`Modules\Xot\Filament\Widgets\XotBaseWidget`** (o **`XotBaseWizardWidget`**) la vista del widget viene risolta nel costruttore della base tramite **`GetViewByClassAction`**.

## regola kiss + dry

1. **Non** dichiarare `protected string $view = 'fixcity::…'` (né `static`) sulla sottoclasse, salvo review architetturale motivata — la base ha già la logica e l’overlay **`pub_theme::…`** primo, modulo secondo (`fixcity::…`, ecc.).
2. **Sì**, opzionale, commento o docblock che elenca le stringhe nella catena (**pigrizia leggibile**) per chi apre il file senza dover inseguire `resolveView()` ogni volta.
3. Se serve forzare **solo** modulo (senza tema), va discusso esplicitamente: sovrascrivere `$view` rompe overlay per design parity e va documentato caso per caso.

## caso reale

`CreateTicketWizardWidget` usa docblock sulla classe; **zero** proprietà `$view`.

## divergenza storica risolta

`docs/ticket-wizard-frontoffice.md` aveva suggerito `fixcity::…` fisso sulla classe: **contraddice** Laraxot. La fonte operativa ora è quel documento dopo la revisione più la presente pagina.

# wizard summary infolist runtime fix 2026-04-22

## contesto

La pagina `http://127.0.0.1:8000/it/tests/segnalazione-crea` ha esposto regressioni nel widget `CreateTicketWizardWidget`.
Il requisito funzionale e architetturale e: `getSummarySchema()` deve usare entry Infolist Filament per il riepilogo read-only, non una Blade `SchemaView`.

## studio

Fonti verificate:

- Filament 5 schema overview: lo schema e il contenitore server-driven che puo includere form fields, layout components, infolist entries e prime components.
- Filament 5 infolists overview: le entry read-only stanno nel namespace `Filament\Infolists\Components` e possono ricevere stato esplicito con `state()`.
- Regola locale Xot `filament/widgets/infolists-for-summary.md`: riepilogo wizard = dati strutturati read-only, quindi `TextEntry` / `ImageEntry`.
- Regola locale Fixcity `rules/filament-wizard-rules.md`: entry `review_*`, stato via `Get`, no `SchemaView` come soluzione primaria, no `->label()` / `->placeholder()` hardcoded.

## diagnosi

Errori da evitare:

1. `Livewire\Forms\Form` non e il form Filament del widget: in questo progetto `XotBaseWidget` espone `Schema $form` tramite `InteractsWithForms` e `statePath('data')`.
2. `Filament\Infolists\Components\Infolist` non e un componente entry da inserire in `Step::schema()`.
3. `SchemaView::make(...)` sposta il riepilogo fuori dal contratto schema e bypassa le entry Infolist.
4. `->label()` hardcoded sugli entry viola la convenzione Laraxot/LangServiceProvider.

## decisione

`getSummarySchema()` deve restituire layout schema (`Section`, `Grid`) contenente entry `Filament\Infolists\Components\TextEntry` e `ImageEntry`.
Le entry usano nomi `review_*` e `->state(fn (Get $get) => ...)` per leggere lo stato corrente del wizard.

## criteri di accettazione

- `CreateTicketWizardWidget` non importa ne istanzia `Livewire\Forms\Form`.
- `CreateTicketWizardWidget` non usa `SchemaView` nel summary.
- `getSummarySchema()` contiene `TextEntry` / `ImageEntry`.
- Il file passa `php -l`.
- La pagina `/it/tests/segnalazione-crea` non fallisce per parse error o classi Filament/Livewire inesistenti.


# Story: Wizard summary step header and submit parity plan

## Context

Target page: `http://127.0.0.1:8000/it/tests/segnalazione-crea?step=form.riepilogo%3A%3Adata%3A%3Awizard-step`

Reference: `https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html`

## Current Findings

- Local page returns HTTP 200.
- The summary step renders `Riepilogo` and `3/3`.
- The markup contains the default Filament wizard footer with a submit button and the custom Design Comuni navigation with `Conferma e invia`.
- Because the default Filament footer is hidden by theme CSS, submit ownership is ambiguous: the user-visible submit must be the Design Comuni final CTA and must call the wizard submit path.

## Header-First Parity Gaps

Reference header/content order:

1. Breadcrumb.
2. `h1` page title: `Segnalazione disservizio`.
3. Stepper: first two steps confirmed, third active.
4. Counter `3/3`.
5. Alert block: `Attenzione` plus declaration warning text.

Local gaps to fix:

- Header stepper exists, but the alert block before the review content is missing.
- Step labels differ from reference concepts: reference uses `Autorizzazioni e condizioni`, `Dati di segnalazione`, `Riepilogo`.
- Summary content starts from Filament schema sections instead of the reference conceptual blocks.

## Body Parity Gaps

Reference summary concepts:

- `Segnalazione`
  - `Disservizio`
  - edit action `Modifica`
  - address, type, title, details, images
- `Dati Generali`
  - `Autore della segnalazione`
  - name, codice fiscale
  - `Contatti`
  - phone, email
- final actions: `Indietro`, `Salva Richiesta`, `Salva`, `Invia`
- terms/confirmation area after submit flow.

Local gaps:

- `getSummarySchema()` currently has a single generic `Riepilogo` section.
- Author data is separated in step 2, but summary needs a read-only author block too.
- The user-visible submit CTA must be one canonical action, not a hidden Filament action plus a custom ambiguous action.

## Implementation Plan

1. Keep `getSummarySchema()` on native Infolist entries (`TextEntry`, `ImageEntry`) and layout `Section`/`Grid` from `Filament\Schemas\Components`.
2. Split summary schema into two conceptual sections: `Segnalazione` and `Dati Generali`.
3. Add the declaration warning before summary content using a schema `Text` component or a reusable callout component, not raw page-specific Blade.
4. Make the visible final CTA call `submit`; hide or neutralize duplicate Filament footer actions only through reusable wizard CSS, not page slug CSS and not `.ticket-wizard-root`.
5. Update theme CSS only with component/site-level selectors.
6. Verify:
   - root summary URL returns 200;
   - only one visible submit CTA exists;
   - header/stepper/alert match reference concepts;
   - no `ViewEntry`, no `SchemaView`, no `Filament\Infolists\Components\Section`.

## Rules Captured

- No CSS scoped to `.page-content[data-slug="tests.segnalazione-crea"]`.
- No CSS scoped to `.ticket-wizard-root` for behavior shared by wizards.
- In Filament 5, `Section` is `Filament\Schemas\Components\Section`; Infolist package provides entries such as `TextEntry` and `ImageEntry`.

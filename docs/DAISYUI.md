# Fixcity Module — daisyUI Reference

## Panoramica

[daisyUI](https://daisyui.com/) è la libreria di componenti UI per Tailwind CSS più popolare al mondo (40.9k ⭐ GitHub).
Il modulo **Fixcity** **non istalla daisyUI direttamente**, ma la consuma indirettamente tramite il **tema Sixteen**.

| Attributo | Valore |
|-----------|--------|
| NPM package | `daisyui@^5.5.19` (canonico nel tema Sixteen, upgrade 2026-05-16 — v4 non builda con Tailwind v4) |
| Installato in Fixcity? | ❌ No (consumato via tema) |
| Installato nel tema Sixteen? | ✅ v5.5.19 in `package.json`, registrato via `@plugin "daisyui"` in `resources/css/app.css` |
| Configurato in Sixteen? | ✅ Tailwind v4 + DaisyUI v5 — config via `@theme` / `@plugin` in CSS (niente più `tailwind.config.js` necessario) |
| Tailwind CSS in Fixcity | ❌ Nessun `tailwind.config.js` locale — usa quello del tema |

---

## Dove Fixcity vede daisyUI

Il modulo Fixcity **non ha un asset pipeline autonomo**. Tutti gli asset CSS/JS sono serviti dal tema Sixteen.
Quindi tutti i benefici di daisyUI sono ereditati dal tema:

1. **Pagina front-office** (`segnalazione-crea`, `appuntamento-*`, ecc.) → `app.css` include regole DaisyUI
2. **Filament Wizard** → Componenti Filament usano classi `fi-*`; daisyUI non interfere direttamente ma fornisce componenti per template ausiliari
3. **Blade views del modulo** → posso usare classi daisyUI (`btn`, `alert`, `card`, `badge`)

### Esempio di utilizzo in Fixcity Blade views

```html
<!-- Usa classi daisyUI invece di Bootstrap Italia -->
<button class="btn btn-primary">Invia segnalazione</button>
<div class="alert alert-warning" role="alert">
    <h4 class="alert-heading">Attenzione</h4>
    <p>Confermi l'invio?</p>
</div>
<div class="card bg-base-100 shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Dettaglio segnalazione</h2>
        <p>...</p>
    </div>
</div>
```

---

## Integrazione con Filament v5 Wizard

Il **Wizard Filament** del modulo (step `privacy`, `data`, `summary`) usa classi `fi-*` generate da Livewire.
DaisyUI e Filament possono convivere, ma servono regole di isolamento:

| Area | classe usata | conflitto con DaisyUI? |
|------|-------------|------------------------|
| Campo input Filament | `fi-input`, `fi-fo-field-wrp` | ✅ No conflitto — regole `fi-*` sono più specifiche |
| Campo select Filament | `fi-fo-select`, `fi-select-input` | ✅ No conflitto — già override con CSS di parità |
| Bottoni Filament | `fi-btn`, `fi-ac-btn-action` | ⚠️ Possibile — DaisyUI usa `.btn`, ma hanno nomi diversi |
| Card generiche (non Filament) | `card`, `card-body` | ✅ OK — clausole daisyUI funzionano |
| Alert/Badge generiche | `alert`, `badge` | ✅ OK |

---

## Pro e contro daisyUI in Fixcity

### ✅ Pro

| Vantaggio | Contesto Fixcity |
|-----------|-----------------|
| Classi `.btn`, `.alert`, `.badge` pronte | Risparmio codice nei template Blade pagine statiche e wizard ausiliari |
| **Design Comuni compliance** | 35 temi built-in — il tema `light` con primary `#007A52` già configurato in Sixteen matcha Design Comuni PA |
| Dark mode nativo | Per pagine di riepilogo, conferma, dettaglio segnalazione — supporto built-in |
| Accessibilità | ARIA roles su `.alert`, `.badge`, `.btn` built-in — meno codice da scrivere |

### ❌ Contro

| Rischi | Impatto in Fixcity |
|--------|-------------------|
| Mix di classi (daisyUI + fi-*) | Incoerenza visiva se daisyUI e Filament condividono stili simili ma non identici |
| BundleSixteen aumenta di ~10–30 KB | Il modulo non ha controllo diretto sul bundle |
| Tailwind v3 vs v4 (incompatibilità) | I componenti Filament v5 sono compilati con Tailwind v4 nel tema, il modulo non ha build propria |
| Dipendenza da Tailwind | Non può essere usato standalone — ma Fixcity non ha assets autonomi, quindi non è un problema |

---

## Percentuali e Metriche

| Metrica | Valore |
|---------|--------|
| Classi `.btn`/`.alert`/`.card` usate in Fixcity views | ~5% delle pagine (stima) |
| Incremento bundle Sixteen se tutte le classi daisyUI abilitate | +15 KB gzip (~10% di `app-xUp1YPU1.css` da 139 KB gzip) |
| Tempo risparmiato vs Bootstrap Italia manuale | ~70% sui template di pagina semplice (stime sviluppatore) |
| Problema: conflitti `.btn` DaisyUI vs `.btn` Bootstrap Italia | Esiste: le pagine Fixcity caricano anche `bootstrap-italia/dist/css/bootstrap-italia.min.css` → attenzione a class name conflict |

---

## Raccomandazioni per Fixcity

| Azione | Priorità |
|--------|----------|
| Usare classi daisyUI (`.btn`, `.input`, `.alert`) nei nuovi template Blade invece di scrivere utility Tailwind manuali | 🔴 Alta |
| Rimuovere `bootstrap-italia/dist/css` dal bundle Sixteen | 🟡 Media (già pianificato in scheda consolidamento) |
| Documentare ogni pagina che usa daisyUI | 🟢 Bassa |
| Non installare daisyUI nel modulo Geo (vedi [Geo/DAISYUI.md](../Geo/docs/DAISYUI.md)) | 🔴 Alta |

---

*Documento: Fixcity/daisyUI-docs — creato 2026-05-16, aggiornato 2026-05-16 (upgrade DaisyUI v5)*
*Modulo: `laravel/Modules/Fixcity/` — NPM: nessun assets autonomo; dipende da Sixteen*

---

## Decisione architetturale (2026-05-16) — riepilogo per Fixcity

Stack canonico: **Tailwind v4 + DaisyUI v5 + Filament v5 + Alpine + Lit**.
**Nomi classi CSS e HTML semantico devono restare allineati a
[italia/design-comuni-pagine-statiche](https://github.com/italia/design-comuni-pagine-statiche/tree/main/src/stylesheets).**

Conseguenze per Fixcity:

- I form Filament (es. `CreateTicketWizardWidget` → `TicketForm`) producono markup
  con classi `fi-*`; le regole di stile vivono nel tema in
  `resources/css/components/filament-wizard-parity.css`.
- Le Blade del modulo che renderizzano UI front-office (pagine `tests/segnalazione-*`)
  devono usare classi design-comuni (`.cmp-card`, `.form-control`, `.steppers-*`,
  `.cmp-input__label`, ecc.), **non** classi Bootstrap-Italia caricate via CDN né
  classi DaisyUI grezze nel markup.
- Lo styling viene da Tailwind utility + componenti DaisyUI, mappati ai nomi
  design-comuni via `@apply` (pattern canonico).

### Lezione "doppio bordo" (rilevante per i wizard Fixcity)

I field del wizard portavano un doppio riquadro: il wrapper Filament v5
`.fi-input-wrp` porta un `ring-1` di base, e le regole locali aggiungevano un
border al `<input>` interno. **Regola operativa: un solo elemento porta il
bordo per ciascun controllo.** Nel wizard il bordo vive sul wrapper, l'input è
trasparente. Vedi `laravel/Themes/Sixteen/resources/css/components/filament-wizard-parity.css`
e il doc canonico `laravel/Themes/Sixteen/docs/DAISYUI.md` § "Decisione architetturale".

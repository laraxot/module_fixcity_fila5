---
title: Context compression and wizard summary Infolist rule
type: concept
updated: 2026-04-22
tags: [fixcity, wizard, infolist, context-compression, llm-wiki]
sources:
  - ../../../../../../docs/wiki/concepts/openrouter-context-compression-plugin.md
  - ../../filament-summary-infolist-guidance.md
  - https://filamentphp.com/docs/5.x/infolists/overview
---

# Context compression and wizard summary Infolist rule

## Regola permanente

`CreateTicketWizardWidget::getSummarySchema()` deve usare entry Infolist Filament 5 (`Filament\Infolists\Components\TextEntry`, ed entry affini quando appropriate) dentro layout schema (`Section`, `Grid`).

Vietato usare `SchemaView` o Blade custom come soluzione primaria del riepilogo. Il summary e' dato read-only strutturato, quindi resta nello schema con stato esplicito via `Filament\Schemas\Components\Utilities\Get`.

## Context discipline

Per evitare errori di contesto troppo lungo:

- non leggere documentazione o log enormi nel prompt;
- aggiornare prima LLM Wiki e docs locali, poi implementare;
- usare QMD/`rg` per recupero mirato;
- ricordare che OpenRouter `context-compression` e' un plugin API (`plugins: [{ "id": "context-compression" }]`), non una dipendenza Laravel.

## DRY/KISS

Una sola fonte di verita per la regola tecnica:

- root wiki: `docs/wiki/concepts/openrouter-context-compression-plugin.md`;
- modulo Fixcity: questa pagina + `docs/filament-summary-infolist-guidance.md`;
- tema Sixteen: pagina design-comuni corrispondente per vincoli visuali, senza duplicare mapping stato.

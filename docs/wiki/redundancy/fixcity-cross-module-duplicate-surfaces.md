---
title: "Superfici Fixcity duplicate cross-modulo (non solo interne)"
type: redundancy
owner: Modules/Fixcity
severity: low-medium
confidence: medium
created: 2026-05-25
related:
  - ../../redundancy-report.md
  - ./duplicated-comments-relation-manager.md
  - ../../../../../Themes/Sixteen/docs/wiki/redundancy/duplicated-blade-blocks.md
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/ridondanze-documentazione-wizard.md
  - ../../../../Xot/docs/wiki/redundancy/audit-profondo-ridondanze-holistic.md
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/90"
---

# Fixcity — dove il dominio pubblico incontra artefatti “copiati” altrove

## Perché questa pagina

Il modulo [**Fixcity**](../../overviews/fixcity-module.md) concentra segnalazioni/ticket. Parte delle view Filament/widget è **delegata correttamente** a Xot (pattern `TicketForm`) e ai temi (boundary parity). Qui si elencano **superfici con hash o struttura condivisi con altri moduli** perché sono candidati alla deriva quando si corregge “solo Rating” o “solo Job” ma non Fixcity — o viceversa.

## Evidenza statica SHA256 cross-owner

- **`resources/views/admin/dashboard/item.blade.php`** modulo **Fixcity** risultato **byte-identico** allo stesso basename sotto modulo **Rating** (scan SHA256 workspace 2026-05-25, vedi [`audit-profondo-ridondanze-holistic.md`](../../../../Xot/docs/wiki/redundancy/audit-profondo-ridondanze-holistic.md)).

**Possibile intento storico**: stesso markup riusato nella dashboard Filament modular-reuse; rischio mantenimento doppio se il markup viene patchato solo in un modulo.

## Evidenza strutturale già nel report modulo

Riassunto tratto da [`Fixcity/docs/redundancy-report.md`](../../redundancy-report.md) (priorità alta interne):

- **`BaseModel`** / **`BasePivot`** dovrebbero estendere rispettivamente `XotBaseModel` / `XotBasePivot` — non è duplicate file ma **duplicate concern** nel layer dati Laraxot.
- **`CommentsRelationManager`** duplicato in due path dentro Fixcity → scheda dedicata **[`duplicated-comments-relation-manager.md`](./duplicated-comments-relation-manager.md)**.

## Boundary doc / tema Sixteen / wizard

Riduzione duplicazioni **non richiede** copiare `TicketForm` nel tema — anzi. La documentazione viene “sliceizzata”; orientamento:

- tema Sixteen: [`ridondanze-documentazione-wizard.md`](../../../../../Themes/Sixteen/docs/wiki/concepts/ridondanze-documentazione-wizard.md)

## Azioni suggerite (business)

1. Ogni volta che si tocca **`admin/dashboard/item.blade.php`** Fixcity confrontare Rating (e altri dashboard item module) prima del merge — o estrarre view condivisa sotto modulo **UI/Xot Filament**.
2. Portare BaseModel/BasePivot allo standard Laraxot (vedi redundancy report) prima di refactor UI che dipendono da factory/trait sul modello.

---
title: "Superfici Fixcity duplicate cross-modulo (non solo interne)"
type: redundancy
owner: Modules/Fixcity
severity: low-medium
confidence: medium
created: 2026-05-25
updated: 2026-05-26
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

## Evidenza strutturale (verificata su sorgenti)

Riallineamento con il codice attuale [`BaseModel`](../../../app/Models/BaseModel.php) e [`BasePivot`](../../../app/Models/BasePivot.php):

- **`BaseModel` Fixcity**: estende già **`XotBaseModel`** con `declare(strict_types=1)` — nessun debito tipo “extends `Model` puro”; restano configurazioni modulo (`$connection`, `SoftDeletes`).
- **`BasePivot` Fixcity**: oggi estende **`Illuminate\Database\Eloquent\Pivot`** con `Updater`/`HasFactory`, mentre altri domini (**Cms, Gdpr, Comment, Blog**) montano **`XotBasePivot`**. Analoghi Pivot “minimali” compaiono anche in Geo/Notify/User. Non sono file gemelli ma **famiglia frammentata** sullo stesso bounded context Laravel: decidere converge verso **`XotBasePivot`** modulo-per-modulo.
- **Namespace lingua** backend vs frontoffice: sintesi in **[`Fixcity/docs/redundancy-report.md`](../../redundancy-report.md)** (`fixcity::ticket` vs fixcity pubblico storico).
- **`CommentsRelationManager`** due path dentro Fixcity → **[`duplicated-comments-relation-manager.md`](./duplicated-comments-relation-manager.md)**.

## Boundary doc / tema Sixteen / wizard

Riduzione duplicazioni **non richiede** copiare `TicketForm` nel tema — anzi. La documentazione viene “sliceizzata”; orientamento:

- tema Sixteen: [`ridondanze-documentazione-wizard.md`](../../../../../Themes/Sixteen/docs/wiki/concepts/ridondanze-documentazione-wizard.md)

## Azioni suggerite (business)

1. Ogni volta che si tocca **`admin/dashboard/item.blade.php`** Fixcity confrontare **Rating** (byte-identico allo scan 2026-05-26) prima del merge — o estrarre view condivisa sotto modulo **UI / Filament tema Xot**.
2. Valutare **`BasePivot` → `XotBasePivot`** in Fixcity dopo audit migration/cast/`$connection`; non toccare `BaseModel` (già allineato a Xot).

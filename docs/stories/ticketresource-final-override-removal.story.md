---
title: "Rimozione override getFormSchemaOld() in TicketResource (Fixcity)"
type: story
status: completed
created: 2026-10-15
updated: 2026-10-15
module: Fixcity
tags: [phpstan, filament, refactor, final, dead-code]
---

# Rimozione override getFormSchemaOld() in TicketResource (Fixcity)

## Richiesta
`phpstan analyse Modules` non partiva a causa dell'override `final` in `TicketResource`. Il modulo doveva compilare per
poter essere analizzato.

## Analisi
- `TicketResource::getFormSchemaOld()` conteneva l'intero form del ticket (name, slug, type, priority, content, location
  CoordinatePicker, images SpatieMediaLibraryFileUpload) — duplicato rispetto a `TicketForm::getFormSchema()`.
- `CreateTicketWidget` richiama `app(TicketResource::class)->getFormSchema()` (che ora ritorna `[]` e delega a `TicketForm`
  tramite il meccanismo standard di Filament). `CreateTicketWizardWidget` fa lo stesso con `TicketForm` direttamente.
- Nessun consumer chiamava `getFormSchemaOld()`.

## Modifiche
- `TicketResource.php`: rimosso l'override e tutti gli import (`Select`, `SpatieMediaLibraryFileUpload`, `Textarea`,
  `TextInput`, `Component`, `Section`, `Get`, `Set`, `Str`, `TicketTypeEnum`, `TicketPriorityEnum`, `CoordinatePicker`).
  Rimane solo `getPages()` con i 4 `ListTickets`/`CreateTicket`/`EditTicket`/`ViewTicket`.

## Verifica
```bash
php -l <file>          # OK
phpstan analyse Modules # 0 errors
```

## Decisioni
- **D-1**: il form del ticket vive solo in `TicketForm` (DCP-184).
- **D-2**: `CoordinatePicker` e `SpatieMediaLibraryFileUpload` sono usati solo da `TicketForm`, non dal Resource.

## Aperto
- Nessuno. I test `CreateTicketWidgetTest`, `TicketFormRenderTest`, `TicketFormValidationTest` usano il widget, non il Resource.
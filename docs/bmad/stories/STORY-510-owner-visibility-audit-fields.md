---
title: STORY-510 — Isolamento ticket privati per owner
type: story
status: in_progress
module: Fixcity
created: 2026-09-26
updated: 2026-09-26
tags:
- bmad
- fixcity
- privacy
- authorization
- owner
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
related:
- ../gap-analysis.md
- ../../wiki/concepts/user-journey-map.md
- ../../wiki/concepts/actor-flow-map.md
qmd: STORY 510 owner visibility audit fields FixCity BMAD story
---

# STORY-510 — Isolamento ticket privati per owner

## Problema

`Ticket::isOwnedByAuthenticatedUser()` usava `created_by` e `updated_by` come
fallback al proprietario. Sono metadati di audit: non attribuiscono ownership e
non devono concedere accesso a ticket privati. Anche il codice di tracking è un capability secret e non deve uscire dall’API pubblica per ticket non posseduti.

## Criteri di accettazione

- [x] Solo `owner_id` concede la visibilità privata del ticket all'utente autenticato.
- [x] Il payload API dettaglio espone il codice-capability solo al proprietario.
- [x] GeoJSON pubblico e aggregato filtri non contengono il codice-capability, nemmeno per i ticket del proprietario.
- [x] Il test copre sia l'owner reale sia l'utente presente solo nei campi audit.
- [x] Test Pest HTTP/API eseguiti sul database SQLite condiviso in transazione.
- [ ] Verificare route CMS del dettaglio e isolamento nel browser con due account; test browser non installato nell'ambiente.

## Verifica

Test API eseguiti: capability code vuoto a guest/non-owner e disponibile al solo
proprietario. L'esecuzione completa Fixcity ha individuato un leak distinto:
`BuildTicketsGeoJsonAction` includeva il capability code nei `properties` e lo
propagava alla mappa/agli aggregati. Il campo è stato rimosso; aggregate, API GeoJSON
e isolamento delle pratiche sono ora coperti e passano (8 test / 35 asserzioni dopo
la localizzazione CTA). L'ultimo run completo, successivo al fix GeoJSON ma precedente
alle correzioni dei test consenso/privacy e del catalogo enum UI, ha riportato 20
fallimenti e 375 passaggi. Le verifiche mirate ora chiudono consenso, privacy e
rendering Filament; il full suite va rilanciato per il conteggio corrente. Il test
MariaDB non usa credenziali valide in questo ambiente; SQLite isolato è il percorso
verificato. Isolamento visuale a due account resta aperto.

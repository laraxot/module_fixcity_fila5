---
title: "Piano BMAD: upload fotocamera mobile"
type: plan
status: approved-for-implementation
updated: 2026-10-27
coordination_story: "../stories/mobile-camera-upload.story.md"
---

# Sequenza

La story BMAD collegata è il registro operativo unico per claim, handoff,
decisioni e verifiche tra Fixcity, Media e Sixteen.

1. **Media contract** — documentare nel modulo Media il confine tra upload
   temporaneo, normalizzazione client e persistenza Spatie; nessuna nuova
   tabella o endpoint.
2. **Server contract** — completare chiavi `ticket_form.fields.images.camera.*`
   in IT/EN/DE/ES e permettere `camera=(self)` solo per il front office.
3. **Theme camera adapter** — implementare modal/inline camera nativa con
   `getUserMedia`, cleanup delle tracce, `playsinline`, canvas JPEG e callback
   tradotti; riusare `send(file)` già esistente.
4. **Filament integration** — aggiungere il pulsante al view custom senza
   cambiare `SpatieMediaLibraryFileUpload`, regole o `saveRelationships()`.
5. **Verification** — test PHPUnit/Pest, build Sixteen, browser autenticato a
   375/768/1440, fixture PHP e Graphify.

## Gate di revisione

Prima del merge verificare che il codice non contenga testo UI in lingua, che
la fotocamera venga fermata su chiusura/navigazione, che il permesso browser
non abiliti microfono e che il fallback picker continui a funzionare.

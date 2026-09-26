# STORY-FIXCITY-DIRECT-ACTION-OVER-RESOURCE — Action diretta nel lifecycle di creazione

**Epic:** Architecture consistency  
**Priorità:** Must  
**Stato:** Implemented

## Decisione

La Page Filament può usare il proprio hook `mutateFormDataBeforeCreate`, ma non deve delegare alla Resource come service locator. La normalizzazione viene chiamata direttamente da `GetTicketFormDataForPersistAction`.

## Criteri di accettazione

- [x] `CreateTicket` invoca direttamente `GetTicketFormDataForPersistAction`;
- [x] `TicketResource` non espone un wrapper statico di business logic;
- [x] la Resource resta responsabile di schema, pagine e configurazione;
- [x] il wizard Frontoffice non dipende dalla Resource come adapter di persistenza;
- [ ] test Feature verifica il payload normalizzato quando il database di test è disponibile.

## GitHub (tracciamento)

| Risorsa | Ruolo |
|---|---|
| [Issue tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/issues/502) | issue refactor da associare |
| [Discussion tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/discussions/503) | decisione Action/Resource |

# STORY-504 — Dashboard Fixcity su XotBaseDashboard

**Epic:** Architecture consistency  
**Priorità:** Must  
**Stato:** Codice implementato; verifica UI runtime pendente

## Criteri di accettazione

- [x] `Modules\\Fixcity\\Filament\\Pages\\Dashboard` estende `Modules\\Xot\\Filament\\Pages\\XotBaseDashboard`;
- [x] nessuna estensione diretta di `Filament\\Pages\\Dashboard`;
- [x] i widget e il layout restano compatibili con il contratto dashboard Xot;
- [ ] verifica browser del dashboard PA con KPI ticket, SLA e rating.

## GitHub (tracciamento)

| Risorsa | Ruolo |
|---|---|
| [Issue tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/issues/502) | issue dashboard da associare |
| [Discussion tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/discussions/503) | decisione wrapper dashboard |

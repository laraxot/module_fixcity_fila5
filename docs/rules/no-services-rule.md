# Actions vs Services Philosophy

## Zen

Il nostro scopo è **DRY + KISS**: una sola via d'uscita per la logica di business.

## Regola Canonica

NO `app/Services/*.php` — usiamo solo **Spatie Queueable Actions**.

### Perché?

| Pattern | Actions | Services |
|---------|---------|----------|
| Testabilità | `Pest` su Action singola | Mock complesso Service container |
| Serializzazione | Può essere messo in queue | NO (stato non serializzabile) |
| Cache | `$ttl` built-in via trait | Da implementare |
| Monitoraggio | Log automatico action lifecycle | Manual |
| Complessità | ZERO — classe con un metodo | Basso-Medio — container injection |

### Struttura

```
app/Actions/
├── BuildSegnalazioniFilterAggregateAction.php  ← SSoT filtri
├── BuildPublicTicketsQueryAction.php         ← Query pubblica
├── LoadDesignComuniElencoFilterCatalogAction.php
├── ResolveTicketTypeMarkerPropertiesAction.php ← Colori/icons tipologia
└── CreateTicketAction.php                    ← Creazione ticket
```

### On-Demand Loading

Ogni Action è **lazy-loaded** via `app(Action::class)->execute()`.

### Pattern per Segnalazioni

La sorgente unica per filtri è la stessa query della mappa:

```
BuildSegnalazioniFilterAggregateAction
    → GeoJSON pubblico (properties.type)
    → countsPerType (conteggi reali)
    → SegnalazioniFilterViewModel::getFilterItems()
```

## Religione

Questa non è una convenzione — è **la nostra religione**.

- Ogni logica di business → Action
- Ogni Action → testabile + queueable
- Viewer → chiama Action, non Service
- NO Service maché per MVC legacy

## Second Brain (LLM Wiki)

Il nostro cervello esterno è in `docs/llm-wiki/`. Ogni Action ha:

- `concepts/<action-name>.md` — filosofia
- `log.md` — evolution
- `index.md` — discovery

## Indice Rapido

| Concetto | File |
|----------|------|
| No Services | `docs/rules/no-services-rule.md` |
| On-Demand | `docs/ON-DEMAND-PATTERN.md` |
| Cache Actions | `docs/llm-wiki/concepts/action-cache-strategy.md`
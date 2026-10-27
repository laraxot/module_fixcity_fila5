---
title: "Tech spec — supporto partner nel footer (post-governance)"
type: tech-spec
status: proposed
updated: 2026-10-15
tags: [bmad, tech-spec, cms, footer, sponsorship]
qmd: "tech spec (non autorizzata all'implementazione) fixcity"
---

# Tech spec (non autorizzata all'implementazione)

Questo documento descrive *come* si implementerebbe dopo il GO di governance.
**Non autorizza codice, migration, Filament Resource o asset.**

## Obiettivo tecnico

Esporre nel footer Sixteen una sezione opzionale «Partner e sostenitori» alimentata
dal CMS tenant (`sections/footer.json` o proiezione equivalente), senza tracking
e senza hardcoded Blade.

## Confine

| Layer | Responsabilità |
|---|---|
| Dominio partner (futuro) | Contratti, scadenze, approvazioni, audit |
| CMS footer JSON | Proiezione pubblica copy + ordine + asset |
| Filament Blueprint | CRUD editoriale + policy pubblicazione |
| Sixteen Blade | Markup, FRONTOFFICE URL, WCAG, empty-state |

## Contratto JSON proposto (bozza)

```json
{
  "support": {
    "enabled": false,
    "title": "Partner e sostenitori",
    "disclosure": "Il servizio è sostenuto da partner indicati in questa sezione. La presenza del logo non costituisce endorsement dei prodotti o servizi.",
    "cta": {
      "label": "Scopri come sostenere FixCity",
      "url": "/it/sostieni"
    },
    "partners": [
      {
        "name": "Nome partner",
        "logo": "/media/partners/esempio.svg",
        "alt": "",
        "url": "https://esempio.example",
        "relationship": "partner",
        "starts_at": "2026-01-01",
        "ends_at": "2026-12-31"
      }
    ]
  }
}
```

Regole: `enabled=false` o `partners=[]` → nessun blocco in UI. URL esterni
marcati; asset solo locali/CDN controllato. Nessun HTML sponsor inline.

## Flusso editoriale

`draft → review → approved → published → expired/revoked`

Pubblicazione richiede: nome, logo valido, alt o nome testuale adiacente,
disclosure, `reviewed_by`, finestre date, tenant.

## Out of scope espliciti

- Ad server, iframe, script o pixel di terzi
- Rotazione/carosello automatico
- Targeting o A/B commerciale
- Modulo Laravel `Sponsor` prima della validazione Epic 1–2
- Donazioni/pagamento nel footer (solo link a pagina dedicata)

## Test gate (quando implementabile)

- Guard: nessun anchor vuoto; nessun logo senza nome accessibile
- Feature: sezione assente se `enabled=false`
- Feature: scadenza rimuove dalla proiezione pubblica
- WCAG: reflow, focus, contrasto testo disclosure, target 44×44
- Privacy: zero cookie/script di advertising nella response

## Dipendenze

[architecture.md](./architecture.md) · [ux-design.md](./ux-design.md) ·
[prd.md](./prd.md) · [recommendation.md](./recommendation.md)

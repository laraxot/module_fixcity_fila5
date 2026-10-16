---
title: "Raccomandazione — modelli per sostenere FixCity"
type: recommendation
status: decision-input
updated: 2026-10-15
---

# Raccomandazione

## In una frase

**Sostenere FixCity prima con contratti e co-funding dei Comuni e con grant;
usare partner trasparenti per credibilità; lasciare donazioni e sponsor commerciali
dopo governance; non fare affidamento sui banner nel footer.**

## Matrice canali

| Canale | Fiducia | Ricavo | Complessità | Priorità |
|---|---:|---:|---:|---|
| Contratto / SaaS B2G per Comune | Alta | Alto ricorrente | Media | **P0** |
| Co-funding multi-comune (pool) | Alta | Alto | Alta | **P0** |
| Grant UE / nazionali / regionali | Alta | Alto ma a progetto | Alta | **P1** |
| Partner istituzionali / in-kind | Alta | Indiretto | Bassa | **P1** |
| Membership / consorzio enti | Alta | Medio ricorrente | Alta | P2 |
| Donazioni cittadino/ETS | Media | Variabile | Alta | P2 |
| Sponsorship commerciale | Media/bassa | Medio | Alta | P3 |
| Banner / ad-tech nel FO | Bassa | Medio | Alta | **No** |

## Perché questo ordine (filosofia)

FixCity è infrastruttura civica: il cittadino non deve «pagare» la segnalazione né
subire advertising per finanziare il servizio. Il modello naturale è quello già
visto in OSS PA (Developers Italia, Signalen, eVaka, Decidim): **enti che
condividono costi di sviluppo e manutenzione**, grant per innovazione, recognition
partner senza endorsement di prodotto.

## Cosa fare subito (solo docs / governance)

1. Nominare owner prodotto + legale per policy partner
2. Definire beneficiario se si parlerà di «sostieni/dona»
3. Scrivere bozza pagina `/sostieni` (contenuto Cms) senza pagamento live
4. Elencare KPI impatto da ticket (modulo Fixcity) usabili in storytelling
5. Decidere opt-in tenant (modulo Tenant)

## Cosa non fare

- Implementare banner sponsor o pixel
- Hardcodare loghi in Blade Sixteen
- Creare modulo `Sponsor` prima di Epic governance
- Paywall sul percorso segnalazione

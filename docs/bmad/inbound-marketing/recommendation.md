---
title: "Raccomandazione — blog ed eventi inbound"
type: recommendation
status: decision-input
updated: 2026-10-15
---

# Raccomandazione

Scopo: [purpose.md](./purpose.md).

## Verdict

**Mettere blog ed eventi nel piano inbound è una buona idea** e coerente con
AFC (attrarre con contenuto utile → qualificare → conversione B2G), adattata al
civico. Completa (non sostituisce) sostenibilità a strati e footer partner.

## Priorità

| Iniziativa | Priorità | Nota |
|---|---|---|
| Piano editoriale post (evergreen PA/cittadino) | **P0** | Module Blog `content_type=post` |
| Eventi pilota (webinar + 1 laboratorio) | **P0** | `content_type=event` + Design Comuni FO |
| Hub risorse «poche e utili» | P1 | Cms page, stile AFC hub |
| Serie email/briefing | P2 | Solo con Notify + Gdpr + consenso |
| Newsletter automatica / nurture lungo | P3 | Dopo policy |
| Banner/ads per traffico | **No** | Conflitto con fiducia |

## Decisioni tecniche (Ponytail)

1. **Un modello**: `Article` con `type` `blog`|`event` — niente modulo Eventi ora
2. **FO**: Sixteen per liste/dettaglio Design Comuni; Meetup per presentazione eventi se già previsto
3. **Authoring**: estendere `ArticleResource` per blueprint, non nuova Resource
4. **CTA**: informazione → evento → adotta; «sostieni» solo se governance

## Cosa fare subito (docs / governance)

- Nominare owner editoriale + revisore accessibilità
- Calendario 90 giorni: 4–6 articoli + 1 evento
- Outline hub risorse e FAQ
- Checklist privacy iscrizione evento

## Cosa non fare

- Implementare nurture email senza Gdpr
- Copiare Elementor/WordPress
- Mescolare sponsor evento nel footer globale
- Paywall sul blog civico

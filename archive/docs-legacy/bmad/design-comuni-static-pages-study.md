---
title: "Studio Design Comuni — pagine statiche per blog, risorse ed eventi"
type: research-report
status: decision-input
created: 2026-10-15
updated: 2026-10-15
tags: [bmad, design-comuni, blog, eventi, risorse, wcag, purpose]
qmd: "Design Comuni argomenti risorse novità eventi lista dettaglio scopo IA"
issues: []
discussions: []
---

# Studio Design Comuni (prima di abilitare Blog/Eventi)

Fonti ufficiali (2026-10-15):

- [Argomenti](https://italia.github.io/design-comuni-pagine-statiche/sito/argomenti.html)
- [Argomento](https://italia.github.io/design-comuni-pagine-statiche/sito/argomento.html)
- [Lista risorse](https://italia.github.io/design-comuni-pagine-statiche/sito/lista-risorse.html)
- [Lista risorse per categorie](https://italia.github.io/design-comuni-pagine-statiche/sito/lista-risorse-categorie.html)
- [Novità](https://italia.github.io/design-comuni-pagine-statiche/sito/novita.html)
- [Novità dettaglio](https://italia.github.io/design-comuni-pagine-statiche/sito/novita-dettaglio.html)
- [Eventi](https://italia.github.io/design-comuni-pagine-statiche/sito/eventi.html)
- [Evento dettaglio](https://italia.github.io/design-comuni-pagine-statiche/sito/evento-dettaglio.html)

## Perché studiarle prima (scopo)

Design Comuni non è un template estetico: è un **contratto cognitivo** tra
cittadino e Comune. Prima di «abilitare il blog» FixCity deve sapere *cosa*
il cittadino cerca, non *come* chiamiamo il modulo Laravel.

| Domanda | Risposta dal modello |
|---|---|
| Cosa cerca il cittadino? | Temi (argomenti), aggiornamenti (novità), occasioni (eventi), documenti utili (risorse) |
| Come si orienta? | Hub temi → dettaglio tema con novità/eventi/servizi → lista filtrabile → dettaglio con indice e feedback |
| Cosa non deve fare FixCity? | Inventare un «Blog marketing» parallelo al vocabolario comunale; banner; gate |
| Religione FO | Vocabolario istituzionale (Novità / Eventi / Argomenti), WCAG, trasparenza |
| Politica prodotto | Inbound civico = fiducia e partecipazione, non pageview |

Senza questo contratto, abilitare Blog/Eventi produce pagine orfane dalla
navigazione comunale e dalla Design Comuni IA già adottata da Sixteen.

## Modello osservato (otto pagine)

| Pagina | Scopo cognitivo | Pattern UI | Contratto FixCity |
|---|---|---|---|
| Argomenti | Orientamento per tema | Card in evidenza + elenco | Pillar/cluster inbound; non duplicare Article |
| Argomento | Hub di un tema | Sezioni Novità / Eventi / Servizi / Documenti | Una vista Cms/tassonomia che *aggrega* |
| Lista risorse | Inventario utile | Evidenza + lista + conteggio + paginazione | Hub Cms «poche e utili» |
| Lista risorse categorie | Inventario + filtro | Lista + tassonomia laterale | Filtro categorie Blog/Cms |
| Novità | Aggiornarsi sul territorio | Evidenza, tipi (notizie/comunicati/avvisi), categorie | `Article content_type=post` |
| Novità dettaglio | Capire un provvedimento/notizia | Breadcrumb, data, indice, a cura di, feedback | Dettaglio articolo |
| Eventi («Vivere il comune») | Scoprire e partecipare | Evidenza date, luoghi, card | `Article content_type=event` |
| Evento dettaglio | Decidere se partecipare | Indice, date/orari, luogo, patrocinio, sponsor distinti | Dettaglio evento trasparente |

### Dettagli strutturali rilevanti

**Argomento (es. Sport):** breadcrumb `Home / Argomenti / Sport`; descrizione;
uffici gestori; blocchi Novità + Eventi + Avvisi; CTA «Tutte le novità».

**Novità dettaglio:** titolo, sottotitolo, data, tempo lettura, condividi,
argomenti, immagine+didascalia, **indice pagina**, sezioni, «A cura di»
(ufficio + persone), ultimo aggiornamento, **feedback chiarezza** 1–5.

**Evento dettaglio:** periodo, argomenti, indice (Cos’è, A chi è rivolto,
Date e orari, Appuntamenti, Patrocinato da), galleria, trascrizione,
contatti/luogo. Sponsor ≠ patrocinio ≠ endorsement.

**Lista risorse:** sommario tipologia; evidenza; «Esplora tutte…»; conteggio
ordinato; card data+titolo+excerpt (+ tipo comunicato).

## Navigazione ufficiale vs FixCity

Nel modello Design Comuni il menu tipico è:

`Amministrazione · Novità · Servizi · Vivere il Comune`

FixCity, prodotto verticale sulle segnalazioni, adatta così:

```text
Segnalazioni · Segnala · Traccia · Novità · Eventi · Domande frequenti
```

- **Etichette cittadino** = Design Comuni (`Novità`, `Eventi`).
- **Path tecnici** = `/blog` e `/events` (SSoT story IM-FIXCITY-004; un solo
  dominio editoriale, non due CMS).
- Header e footer li espongono dai JSON section — **nessun link hardcoded**
  in Blade (contratto Sixteen header/footer).

## Decisioni (BMAD)

1. **Non abilitare** liste/dettagli FO finché i gate sotto non passano.
2. Navigazione: aggiornare `header.json` + `footer.json` (tutte le locali) —
   meccanismo già verificato via `<x-section>`.
3. Dominio: riuso `Blog\Article` `content_type=post|event` (Ponytail / Blueprint) —
   nessun modulo Eventi.
4. Argomenti/risorse: Cms + tassonomia; non copiare articoli in un secondo store.
5. Sponsor/patrocinio: campi/contenuti distinti; no banner commerciali nel
   flusso novità/eventi.
6. Feedback chiarezza (1–5) e «a cura di»: in scope UX Design Comuni; implementazione
   deferred finché lista/dettaglio esistono.

## Mapping tecnico (Ponytail + Filament Blueprint)

| Bisogno Design Comuni | Asset esistente | Gap |
|---|---|---|
| Lista novità | Article + Theme queries | Form/table type; FO Folio `/blog` |
| Dettaglio novità | Article show | Template Design Comuni parity |
| Lista eventi | Article + `event_*` dates | Filtro `content_type=event`; FO `/events` |
| Dettaglio evento | Article show event | Campi patrocinio/costi in docs; no nuova tabella |
| Argomenti | Categorie / Cms hub | Hub aggregatore (IM-CMS-001) |
| Nav header/footer | sections JSON | **fatto** (locale + Fixcity + eu) |

Blueprint: [inbound-blog-events.md](../../../Blog/docs/blueprints/inbound-blog-events.md).  
Audit: [inbound-blog-events-ponytail-audit.md](../../../Blog/docs/blueprints/inbound-blog-events-ponytail-audit.md).

## Gate prima dell'abilitazione

- [x] Route pubblica localizzata `/blog` risolve (lista + dettaglio)
- [x] Route pubblica localizzata `/events` risolve (lista + dettaglio)
- [x] Query FO Novità filtrano `content_type=post`; Filament form/table `post|event`.
- [x] Header/footer JSON contengono Novità/Eventi in IT/EN/DE/ES (local + Fixcity + eu).
- [ ] Liste, dettagli, breadcrumb, indice, empty state, feedback accessibili.
- [ ] WCAG 2.2 AA, tenant isolation, privacy evento, nessun link `#`.



## Lettura approfondita 2026-10-15 (sessione)

Osservazioni dalle pagine live (non solo titoli):

### Argomenti / Argomento
- **Argomenti** = organizzazione del sito istituzionale *per temi*, non un blog.
  Card «in evidenza» + griglia; descrizioni brevi.
- **Argomento** = hub trasversale: descrizione, uffici/contatti, poi **Novità**,
  **Eventi/Avvisi**, **Amministrazione**, **Servizi**, **Documenti**.
  Implicazione FixCity: pillar inbound devono poter aggregare ticket FAQ +
  articoli + eventi, non solo elenchi Article.

### Lista risorse / con categorie
- Pattern generico riusabile: hero sommario → evidenza → «esplora tutte» →
  lista card (tipo + data + titolo + excerpt) ± filtro categorie laterale.
- È il contratto UI per hub Cms e per liste Novità filtrabili — non un CMS
  separato.

### Novità / Novità dettaglio
- Tipologie istituzionali: **Notizie**, **Comunicati**, **Avvisi** (badge).
- Categorie laterali (Notizie / Comunicati / Bandi…) = navigazione per bisogno.
- Dettaglio: data, didascalia, corpo normativo/informativo, ufficio, persone,
  ultimo aggiornamento. Feedback chiarezza nel modello ufficiale.

### Eventi / Evento dettaglio
- Lista sotto framing **«Vivere il comune»** (scoprire territorio), non
  «calendario marketing».
- Evidenza con **giorno/mese** tipografici; card luoghi/iniziative.
- Dettaglio: periodo festival, «Cos’è», partecipanti, galleria, orari, costi/
  tribune (quando applicabile), **Patrocinato da** distinto da sponsor,
  contatti organizzatore + ufficio. Ultimo aggiornamento.

### Implicazione nav FixCity (verificata)

Header/footer JSON (module + local + eu, IT/EN/DE/ES) già espongono:

| Etichetta cittadino | Path tecnico | Dove |
|---|---|---|
| Novità / News / … | `/blog` | `navigation[]` header + colonne/gruppi footer |
| Eventi / Events / … | `/events` | idem; footer group `living` = «Vivere il comune» |

**Abilitazione FO ancora vietata**: le route pubbliche `/blog` e `/events` non
esistono (solo admin Blog). I link JSON anticipano il contratto; non spezzare
Blade con hardcode. Quando le route nasceranno, basterà che risolvano — i JSON
sono già pronti (Existing Assets First / Ponytail sulla section).


## Collegamenti

- Story: [inbound-design-comuni-information-architecture.story.md](stories/inbound-design-comuni-information-architecture.story.md)
- Inbound purpose: [inbound-marketing/purpose.md](inbound-marketing/purpose.md)
- Sixteen contract: [header-footer-sections-contract.md](../../../../Themes/Sixteen/docs/bmad/header-footer-sections-contract.md)
- IA generale: [design-comuni-information-architecture.md](design-comuni-information-architecture.md)

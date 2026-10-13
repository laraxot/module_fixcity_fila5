---
title: "DesignComuni information architecture — Fixcity"
type: bmad-architecture
status: active
created: 2026-10-11
updated: 2026-10-11
tags: [bmad, design-comuni, information-architecture, fixcity, services, practices]
sources:
  - "https://designers.italia.it/files/resources/modelli/comuni/adotta-il-modello-di-sito-comunale/definisci-architettura-e-contenuti/Architettura-informazione-sito-Comuni.ods"
  - "https://italia.github.io/design-comuni-pagine-statiche/"
qmd: "architettura informativa fixcity"
---

# Architettura informativa

## Fonte e implicazione

Il modello ODS v2.0 di Designers Italia descrive l’architettura informativa dei
siti comunali attraverso ontologia, tassonomie, coreografia e sistema di
navigazione. Il front office Fixcity deve quindi presentare le segnalazioni
all’interno della navigazione comunale, senza trasformare le card ticket in un
catalogo autonomo scollegato da servizi, pratiche e punti di contatto.

## Mapping Fixcity

| Modello comunale | Uso nel progetto |
| --- | --- |
| **Servizio** | ingresso istituzionale al servizio “Segnala un problema” e ai servizi collegati |
| **Pratica** | ciclo di vita della segnalazione: invio, presa in carico, aggiornamenti, chiusura |
| **Punto di contatto** | canali, email, telefono, ufficio e contatti per assistenza |
| **Luogo** | posizione della segnalazione e mappa pubblica |
| **Messaggio** | comunicazioni di stato e avvisi verso il cittadino |
| **Documento pubblico** | privacy, accessibilità, regolamenti e istruzioni |
| **Notizia/Evento** | contenuti editoriali eventualmente pubblicati nel CMS comunale |

“Segnalazione” è un’estensione del dominio Fixcity e non una nuova tipologia
ufficiale del modello ODS. Va esposta come pratica/servizio civico, conservando
il vocabolario pubblico comprensibile “Segnala un problema”.

## Coreografia delle pagine

```text
Homepage -> Servizi -> Segnala un problema -> Privacy -> Dati -> Riepilogo -> Conferma
Homepage -> Segnalazioni pubbliche -> Lista/Mappa -> Dettaglio -> Traccia aggiornamenti
Homepage -> Contatti / Punto di contatto -> assistenza e accessibilità
```

La pagina non deve richiedere al cittadino di conoscere ID tecnici, slug CMS o
la struttura interna dei moduli. Le route legacy possono restare alias tecnici,
ma la navigazione e i titoli devono seguire il vocabolario sopra.

## Acceptance criteria BMAD

- Ogni pagina FO è assegnata a una tipologia ODS o documentata come estensione
  Fixcity.
- Il flusso `/segnalazione` è raggiungibile dal servizio, non solo dalla mappa.
- Ogni schermata di errore indica servizio/punto di contatto per il recupero.
- Il tema implementa struttura e stati; il modulo possiede dati, permessi e
  transizioni della pratica.
- Le tassonomie pubbliche (categoria, stato, luogo) sono coerenti tra lista,
  mappa, dettaglio e tracking.

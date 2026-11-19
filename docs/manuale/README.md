---
title: "Manuale d'uso FixCity"
type: manual
audience: tutti
updated: 2026-11-10
---

# Manuale d'uso FixCity

FixCity è il portale con cui i cittadini segnalano al Comune un problema sul territorio, ad esempio una buca, un lampione
spento o i rifiuti abbandonati, e ne seguono la soluzione. Il personale del Comune riceve le segnalazioni in un pannello di
gestione, le valuta, le assegna e aggiorna lo stato fino alla chiusura.

Il manuale è diviso per tipo di utente. Ognuno legge solo la sua guida, più la panoramica.

| Se sei… | Cosa fai con FixCity | La tua guida | PDF |
| --- | --- | --- | --- |
| Chiunque | Stati di una segnalazione, domande frequenti, problemi noti | [Panoramica](panoramica.md) | [PDF](pdf/panoramica.pdf) |
| Un cittadino, anche senza account | Guardi le segnalazioni, ne invii di nuove, le segui | [Cittadini](cittadini.md) | [PDF](pdf/cittadini.pdf) |
| Un dipendente del Comune che gestisce le segnalazioni | Approvi, assegni, aggiorni lo stato, rispondi | [Operatori](operatori.md) | [PDF](pdf/operatori.pdf) |
| Un capo ufficio, o il responsabile di una segnalazione | Coordini gli operatori o lavori le segnalazioni assegnate a te | [Supervisori e responsabili](supervisori-e-responsabili.md) | [PDF](pdf/supervisori-e-responsabili.pdf) |
| Chi gestisce accessi e contenuti per il Comune | Dai i ruoli, aggiorni le pagine, parli con il fornitore | [Amministratori](amministratori.md) | [PDF](pdf/amministratori.pdf) |

Tutto il manuale in un solo file: [manuale-fixcity.pdf](pdf/manuale-fixcity.pdf).

## Per chi mantiene il manuale

- **Fonte:** questi file Markdown. La versione online condivisibile è un documento Claude
  ([Manuale d'uso FixCity](https://claude.ai/artifact/XHExSba9dg6k78bfFqYdF8)); quando cambia uno, va allineato l'altro.
- **Screenshot:** in `img/`, presi da fixcity.eu il 5 novembre 2026 e annotati con cerchi rossi numerati. Mancano ancora le
  schermate del modulo di segnalazione, dell'area personale e dei pannelli di gestione: servono un account di prova e il
  rilascio del branch `dev`.
- **PDF:** si rigenerano con `python3 manuale/build-pdf.py` dalla cartella `docs/` del modulo (serve `pandoc` e Chromium
  con Playwright).
- **Diagramma degli stati:** sorgente Mermaid in `img/stati-segnalazione.mmd`, immagine con
  `mmdc -i stati-segnalazione.mmd -o stati-segnalazione.png -w 1400 -b white`.

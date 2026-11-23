---
title: "Manuale d'uso FixCity"
type: manual
audience: tutti
updated: 2026-11-23
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

- **Fonte:** questi file Markdown; i PDF sono derivati da qui. Il documento online esterno non è una fonte verificabile del
  repository e non viene usato per dichiarare funzionalità.
- **Screenshot:** le immagini JPG annotate preesistenti restano nella cartella `img/`. Il 23 novembre 2026 sono state
  verificati anche il front office pubblico, l'elenco delle segnalazioni e la pagina di accesso su `fixcity.eu`; le catture
  PNG corrispondenti sono `cittadini-01-pagina-iniziale.png`, `cittadini-03-segnalazioni-mappa.png` e
  `cittadini-07-accedi.png`. Non sono stati inventati screenshot del pannello autenticato: per quello serve un account di
  prova con i ruoli effettivi.
- **PDF:** si rigenerano con `python3 manuale/build-pdf.py` dalla cartella `docs/` del modulo (serve `pandoc` e Chromium
  con Playwright).
- **Diagramma degli stati:** sorgente Mermaid in `img/stati-segnalazione.mmd`, immagine con
  `mmdc -i stati-segnalazione.mmd -o stati-segnalazione.png -w 1400 -b white`.

---
title: "Studio approfondito — AFC Formazione e Alessandro Ferrari"
type: bmad-research-report
status: discovery
updated: 2026-10-15
tags: [bmad, inbound-marketing, content-strategy, blog, civic-tech]
qmd: "scopo fixcity"
---

# Scopo

Capire non che cosa copiare, ma quale sistema rende il contenuto utile e porta
una persona dal problema reale alla relazione successiva. Sono stati analizzati
gli indici e alcuni articoli dei due ecosistemi il 15 ottobre 2026.

Dossier canonico correlato:
[../inbound-marketing/README.md](../inbound-marketing/README.md) ·
[../inbound-marketing/afc-formazione-pattern-analysis.md](../inbound-marketing/afc-formazione-pattern-analysis.md).

## 1. AFC Formazione — il modello editoriale operativo

Fonte: [afcformazione.it/blog](https://www.afcformazione.it/blog/).

### Architettura osservata

- promessa editoriale chiara: “Blog di Alessandro Ferrari” e temi centrati su
  comunicazione, leadership e organizzazione;
- card recenti con titolo concreto, categoria e collegamento all'articolo;
- categorie come “Comunicazione Efficace”, “Leadership Management” e “Tecniche
  di Comunicazione on line”;
- footer e sidebar che collegano articoli, risorse gratuite, servizi, autore e
  contatto;
- CTA ricorrente “Comunicazione aziendale sotto controllo” e strumenti gratuiti;
- ecosistema riconoscibile: articolo → guida/test → contatto o formazione.

### Struttura dell'articolo osservato

Fonte: [Differenza tra capo e leader](https://www.afcformazione.it/blog/differenza-tra-capo-e-leader/).

La sequenza osservata è:

1. titolo SEO e promessa concreta;
2. problema spiegato con esperienza e contesto;
3. confronto o griglia pratica;
4. criteri applicabili e sezioni brevi;
5. strumento interattivo/checklist;
6. FAQ;
7. prossimo passo e CTA;
8. autore, articoli correlati e risorse.

Il valore non è il volume degli articoli: è la trasformazione di una domanda in
un metodo verificabile.

## 2. Alessandro Ferrari — il modello strategico B2B

Fonte: [aferrari.it/articoli](https://www.aferrari.it/articoli/).

### Architettura osservata

- indice “Articoli” posizionato come area di strategia;
- cluster su customer journey, marketing, AI, governance, retention, referral,
  KPI e processi;
- articoli recenti distinti dai contenuti strategici principali;
- collegamenti tra siti dell'ecosistema: AFC per il test comunicazione e ASSO per
  il playbook vendite;
- CTA e link con UTM espliciti per capire la provenienza senza confondere il
  contenuto con la pubblicità;
- lead magnet tematici, come playbook e check-up, collocati dopo una promessa
  editoriale coerente.

### Articolo strategico osservato

Fonte: [Inbound marketing per PMI](https://www.aferrari.it/blog/inbound-marketing-pmi/).

L'articolo esplicita sei fasi: problema, contenuto, conversione, qualificazione,
nurturing e vendita. Propone inoltre un piano 30/60/90 giorni e distingue le
metriche di attenzione dai risultati. Questo è il principio più utile per
FixCity: ogni contenuto deve avere un passo successivo proporzionato, non una
CTA economica automatica.

## 3. Cosa trasferire a FixCity

| Pattern osservato | Adattamento civico | Cosa non trasferire |
|---|---|---|
| Pillar e cluster | problemi cittadini, lavoro PA, accessibilità, impatto | linguaggio commerciale |
| Checklist e strumenti | guide per segnalare bene, gestire e rendicontare | gate aggressivi |
| CTA progressive | guida → evento → demo → adozione/collaborazione | lead scoring individuale |
| Ecosistema di siti | Blog, Meetup, documentazione, pagina progetto | tracking cross-site |
| Piano 90 giorni | calendario editoriale sostenibile | promessa di risultati non misurati |
| UTM espliciti | solo se compatibili con privacy e consenso | pixel e profilazione |
| FAQ e autore | responsabilità, fonti, data revisione, contatti | autore fittizio o endorsement |

## 4. Insight di prodotto

FixCity non deve “vendere contenuti”: deve rendere evidente il valore del
servizio pubblico. Il funnel corretto è:

```text
problema civico → guida utile → prova/caso → evento o demo → adozione/collaborazione
                                      ↘ pagina sostegno trasparente
```

Le persone hanno intenzioni diverse. Un cittadino cerca una risposta; un
operatore cerca una procedura; un Comune cerca evidenze, costi e governance; un
grant maker cerca impatto e rendicontazione. Una CTA unica per tutti sarebbe
fuorviante.

## 5. Gap da validare nel progetto

- il Blog esiste e ha `ArticleResource`, ma il contratto editoriale inbound non
  è ancora una policy unica;
- i campi `type`, `event_start_date` e `event_end_date` consentono un pilota
  eventi senza schema duplicato, come documentato nel Blueprint;
- occorre verificare tenant isolation e stato pubblicazione prima di mostrare
  contenuti a più Comuni;
- luogo, modalità, accessibilità e iscrizione evento non hanno ancora un
  contratto approvato;
- non sono disponibili baseline di conversione FixCity: non inventare KPI
  numerici prendendoli dai siti osservati.

## Fonti e limiti

- https://www.afcformazione.it/blog/
- https://www.afcformazione.it/blog/differenza-tra-capo-e-leader/
- https://www.aferrari.it/articoli/
- https://www.aferrari.it/blog/inbound-marketing-pmi/

Le conclusioni sono un'analisi qualitativa di pagine pubbliche. Non implicano
che i tassi di conversione, gli strumenti o le pratiche privacy dei siti
osservati siano noti o adatti a FixCity.

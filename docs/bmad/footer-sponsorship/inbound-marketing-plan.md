---
title: "Piano BMAD — inbound marketing FixCity"
type: bmad-plan
status: proposed-for-validation
updated: 2026-10-15
tags: [bmad, inbound-marketing, blog, eventi, content-operations, filament]
---

# Obiettivo

Costruire un sistema editoriale che trasformi domande civiche in comprensione,
partecipazione e adozione del servizio. Il primo risultato non è monetizzare il
footer: è generare fiducia verificabile e conversazioni qualificate con Comuni,
associazioni e comunità.

## Posizionamento editoriale

**Promessa:** FixCity aiuta una comunità a rendere visibili i problemi,
organizzare la presa in carico e comunicare i risultati in modo accessibile.

### Pillar

1. **Cittadino attivo** — come descrivere un problema, seguire una risposta,
   rispettare privacy e usare il servizio;
2. **Comune operativo** — workflow, priorità, ruoli, tempi e qualità della presa
   in carico;
3. **Comunità accessibile** — WCAG, linguaggio semplice, inclusione e canali
   alternativi;
4. **Dati e impatto** — metriche aggregate, open data, casi d'uso e
   rendicontazione;
5. **Progetto sostenibile** — governance, partnership, grant e uso trasparente
   delle risorse.

## Formati

| Fase | Formato | CTA proporzionata |
|---|---|---|
| Scoperta | articolo breve, glossario, FAQ | leggi una guida correlata |
| Comprensione | guida, checklist, caso d'uso | scarica/consulta la risorsa |
| Relazione | webinar, laboratorio, demo | partecipa all'evento |
| Valutazione | pagina adozione, documentazione, demo PA | parla con il team |
| Sostegno | report impatto, pagina sostieni | scopri come contribuire |

Nessun contenuto richiede registrazione per essere letto. Iscrizioni evento,
newsletter o contatti richiedono informativa, consenso dove necessario,
disiscrizione e minimizzazione dei dati.

## Piano 90 giorni

### Giorni 1–30 — diagnosi e fondazioni

- definire owner, revisore e glossario civico;
- mappare domande reali da ticket anonimi, FAQ e interviste;
- fissare i cinque pillar e le categorie Article;
- definire `blog`/`event` e il contratto minimo dell'evento;
- scegliere 3 articoli pilastro e 6 contenuti cluster;
- approvare checklist WCAG, privacy, fonti, alt text e CTA.

### Giorni 31–60 — percorso minimo

- pubblicare gli articoli pilastro con struttura problema → metodo → esempio →
  FAQ → prossimo passo;
- collegare ogni articolo a un solo cluster e una sola CTA primaria;
- progettare un webinar o laboratorio accessibile;
- pubblicare pagina evento con programma, modalità, accessibilità, organizzatore
  e privacy;
- raccogliere feedback qualitativo, non profilazione.

### Giorni 61–90 — apprendimento

- svolgere l'evento e pubblicare materiali/esiti;
- raccogliere richieste qualificate di adozione o collaborazione;
- aggiornare articoli secondo domande emerse;
- pubblicare un primo report impatto con dati aggregati;
- decidere se il modello passa da discovery a implementazione controllata.

## Governance contenuti

Ogni articolo o evento deve avere: pubblico, problema, owner, revisore, fonti,
locale, stato, data pubblicazione, data revisione, CTA, eventuale disclosure e
criteri di archiviazione. Il Blog module gestisce il contenuto; Meetup presenta
gli eventi; il footer JSON non diventa un menu promozionale.

## Misurazione

Stabilire la baseline prima dei target. KPI iniziali:

- utilità dichiarata e comprensione del contenuto;
- completamento di guida o FAQ;
- partecipazione e presenza agli eventi;
- richieste qualificate da enti o associazioni;
- tempo editoriale per contenuto;
- aggiornamenti e contenuti archiviati correttamente;
- reclami, cancellazioni e richieste privacy.

Evitare come KPI principale visualizzazioni, follower, click non qualificati o
lead scoring individuale.

## Filament Blueprint

Il piano tecnico è in
[`inbound-blog-events.md`](../../laravel/Modules/Blog/docs/blueprints/inbound-blog-events.md).
La scelta Ponytail è riusare `ArticleResource` e i campi evento esistenti. Prima
del codice servono readiness, policy tenant e conferma della tassonomia.

## Gate BMAD

`CONCERNS`: il piano è pronto per revisione product/tenant owner, non per deploy.
Il passaggio a implementazione richiede owner editoriale, tassonomia approvata,
contratto evento, isolamento tenant dimostrato e test accessibilità/privacy.

## Anti-pattern esclusi

- replicare il modello commerciale AFC/aferrari;
- pubblicare quantità di articoli senza percorso;
- CTA “sostieni” in ogni contenuto;
- sponsor non dichiarati negli eventi;
- newsletter o tracking impliciti;
- contenuti AI senza revisione umana e fonti.

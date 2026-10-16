---
title: "Inbound marketing — blog ed eventi per FixCity"
type: bmad-research-and-strategy
status: discovery
updated: 2026-10-15
tags: [bmad, inbound-marketing, blog, eventi, civic-tech, accessibility]
---

# Perché blog ed eventi

Blog ed eventi sono più adatti dei banner a sostenere FixCity: spiegano il
problema, mostrano risultati verificabili e costruiscono fiducia prima di
chiedere ad un Comune, ad un ente o ad un sostenitore di collaborare. Devono
restare contenuti editoriali separati dal footer globale.

## Osservazione del riferimento AFC Formazione

La pagina pubblica di [AFC Formazione](https://www.afcformazione.it/) mostra un
modello riconoscibile:

- promessa chiara e verticale nella title/description;
- articoli collegati a temi specifici, non un blog generico;
- risorse gratuite e guide come passaggio intermedio;
- CTA ricorrenti verso una risorsa o un contatto;
- pagina autore, servizi e contatti che rendono credibile la proposta;
- feed RSS, metadati Open Graph e dati strutturati che favoriscono la scoperta.

Questa è un'osservazione del sito visitato il 2026-10-15, non una prescrizione
di copiare il suo stack WordPress, i suoi plugin o il suo tracking. Per FixCity
va adattata ad un servizio civico: meno pressione commerciale, più trasparenza,
privacy by design e contenuti utili a cittadini, tecnici e amministratori.

## Funnel proposto

```text
Problema locale → articolo/guida → caso d'uso o evento → contatto qualificato
                 → demo/documentazione → adozione o grant → rendicontazione
```

### Blog

Il Blog module è il primo candidato per:

- guide pratiche su segnalazioni, accessibilità, open data e tempi di risposta;
- casi d'uso anonimi e misurabili;
- spiegazioni per amministratori e operatori;
- changelog narrato delle funzionalità che cambiano il servizio;
- FAQ che riducono il carico su supporto e uffici.

Ogni articolo dovrebbe avere persona destinataria, problema, evidenza, CTA
successiva e data di revisione. La CTA può essere “leggi la guida”, “partecipa
all'evento” o “parla con il team”; non deve essere una richiesta economica
prematura.

### Eventi

Gli eventi, resi dal tema Meetup o da un futuro dominio dedicato, sono il ponte
tra contenuto e relazione:

- webinar dimostrativi per Comuni e associazioni;
- laboratori locali di mappatura dei problemi;
- sessioni su WCAG, dati aperti e partecipazione;
- incontri con partner istituzionali, con disclosure chiara;
- follow-up pubblico con materiali, esiti e prossimi passi.

Un evento deve avere luogo/modalità, accessibilità, organizzatore, programma,
relatori, privacy dell'iscrizione e criterio di cancellazione. Uno sponsor di
un evento va indicato come sponsor dell'iniziativa, non come approvatore di
FixCity né come sponsor permanente del servizio.

## Contenuto e conversione

La sequenza minima consigliata è:

1. contenuto utile senza gate obbligatorio;
2. risorsa scaricabile o evento con iscrizione facoltativa;
3. pagina “collabora/adotta” per interlocutori qualificati;
4. pagina trasparente “sostieni” solo quando beneficiario e rendicontazione sono
   reali;
5. aggiornamento pubblico su risultati e uso dei fondi.

Newsletter e reminder non sono impliciti: richiedono consenso, preferenze,
disiscrizione e base giuridica documentata. Il primo pilota deve funzionare
anche senza cookie advertising, pixel o profilazione.

## Metriche sane

Misurare qualità e fiducia, non solo traffico:

- articoli completati e ricerche interne riuscite;
- partecipazione e presenza agli eventi;
- download/accessi alla documentazione;
- richieste qualificate di adozione o collaborazione;
- comprensione della disclosure e soddisfazione;
- tempo e costo editoriale per contenuto;
- cancellazioni, reclami e richieste privacy.

Le metriche vanno aggregate per tenant e periodo. Niente profili individuali per
decidere chi vede una CTA economica.

## Gate BMAD

Questa strategia è **GO per discovery editoriale**, non per una nuova Resource,
una newsletter automatica, pagamenti o sponsor nel footer. Prima di costruire:

- [ ] owner editoriale e revisore nominati;
- [ ] calendario pilota blog/eventi approvato;
- [ ] policy privacy e iscrizione evento definite;
- [ ] modello di disclosure partner/sponsor pubblicato;
- [ ] criteri di accessibilità e misurazione verificabili;
- [ ] contenuti localizzati e compatibili con isolamento tenant;
- [ ] readiness BMAD aggiornata a seguito dei test con utenti.

## Fonti

- AFC Formazione, sito osservato: https://www.afcformazione.it/
- Analisi approfondita (home, blog, hub risorse):
  [../inbound-marketing/afc-formazione-pattern-analysis.md](../inbound-marketing/afc-formazione-pattern-analysis.md)
- Dossier inbound canonico: [../inbound-marketing/README.md](../inbound-marketing/README.md)
- W3C WCAG 2.2: https://www.w3.org/TR/WCAG22/
- GDPR, Regolamento (UE) 2016/679: https://eur-lex.europa.eu/eli/reg/2016/679/oj
- Designers Italia, contenuti e footer PA: https://designers.italia.it/design-system/componenti/footer/

## Limiti

Non sono stati inventati tassi di conversione, dimensioni del mercato o
risultati attribuiti ad AFC. La pagina AFC è un pattern qualitativo osservato;
il modello FixCity va validato con cittadini, operatori e Comuni.

---
title: "FixCity - Guida per gli operatori del Comune"
type: manual
audience: operatori
updated: 2026-11-10
---

# Guida per gli operatori del Comune

Questa guida è per chi in Comune riceve le segnalazioni dei cittadini e le porta fino alla soluzione. Ogni passaggio è
spiegato come se aprissi il pannello per la prima volta.

## Cosa ti serve

- **Un account** su FixCity con la tua email di lavoro, confermata.
- **Due ruoli**, assegnati dall'amministratore: `operator`, che ti permette di lavorare sulle segnalazioni, e
  `fixcity::admin`, che ti fa entrare nel pannello. Con uno solo dei due non riesci a lavorare.
- **Un computer**: il pannello funziona anche da tablet, ma su uno schermo grande è più comodo.

Se non hai un account o i ruoli giusti, chiedi all'amministratore di FixCity del tuo Comune. La procedura che segue lui è
nella [guida per gli amministratori](amministratori.md).

Le schermate del pannello verranno aggiunte dopo il prossimo rilascio: al 10 novembre 2026 il pannello su fixcity.eu
risponde con un errore, già corretto e in attesa di pubblicazione.

## Entrare nel pannello

1. Apri il browser e scrivi l'indirizzo del sito seguito da `/fixcity/admin`, ad esempio `fixcity.eu/fixcity/admin`.
   Salvalo nei preferiti: è il tuo punto di partenza ogni giorno.
2. Scrivi email e password, poi premi **Accedi**.
3. Si apre il pannello. A sinistra c'è il menu, al centro i riquadri con i numeri principali: segnalazioni aperte, tempi di
   risposta, valutazioni dei cittadini.

Se vedi una pagina di errore o "non autorizzato", probabilmente ti manca uno dei due ruoli: avvisa l'amministratore.

## Trovare e aprire una segnalazione

1. Nel menu a sinistra premi **Segnalazioni**.
2. Vedi una tabella: una riga per segnalazione, con codice, titolo, tipo, stato e data.
3. Per trovarne una:
   - scrivi una parola del titolo o il codice nella casella di **ricerca** in alto;
   - usa i **filtri** per vedere, ad esempio, solo quelle **In attesa**;
   - premi il titolo di una colonna per ordinare, ad esempio per data.
4. Premi una riga per aprire la **scheda della segnalazione**: dati, foto e allegati, posizione sulla mappa, cronologia e
   commenti.

**Consiglio per ogni mattina:** filtra per stato **In attesa**. Sono le segnalazioni nuove che nessuno ha ancora guardato.

Il pulsante **Esporta elenco** scarica l'elenco in un file da aprire con Excel, utile per i resoconti.

## Inserire una segnalazione arrivata allo sportello o al telefono

Se un cittadino segnala di persona o per telefono, puoi inserire tu la segnalazione dal pannello con **Nuova segnalazione**.

- Stato e priorità non si scelgono nel modulo: la segnalazione nasce sempre **In attesa** con priorità bassa, come quelle
  inviate dai cittadini. Poi la approvi e la porti avanti come le altre.
- Riceve subito il suo codice `TCK-…`: comunicalo al cittadino, che lo usa nella pagina **Traccia**.
- Oggi la segnalazione risulta inserita da te: la ricevuta via email arriva a te, non al cittadino.

## Approvare o scartare una segnalazione nuova

Ogni segnalazione nuova è **In attesa** e non è visibile al pubblico. Il tuo primo compito è decidere se è valida.

**Approvare**

1. Apri la segnalazione e controlla testo, foto e posizione.
2. Premi **Approva** in alto nella scheda.
3. Nella finestra che si apre, scrivi il **Motivo dell'approvazione**, ad esempio "Presa in carico dall'Ufficio Strade".
4. Conferma. La segnalazione passa a **In revisione** e diventa pubblica, quindi visibile sulla mappa. Le foto diventano
   visibili a tutti; i PDF allegati restano visibili solo al cittadino che li ha inviati e al personale del Comune.

**Scartare**

Scarta le segnalazioni doppie, fuori competenza del Comune, offensive o senza senso.

1. Premi **Scarta**.
2. Scrivi il **Motivo dello scarto**, ad esempio "Già segnalata con il codice TCK-…" oppure "Strada provinciale: di
   competenza della Provincia".
3. Conferma. La segnalazione resta **In attesa**, quindi non pubblica, e il motivo resta nella cronologia. Si può scartare
   anche una segnalazione già **In revisione**: torna non pubblica.

**Attenzione alle foto:** prima di approvare guarda se nelle foto ci sono volti o targhe leggibili. Oggi FixCity non ha uno
strumento per oscurarli: in quel caso chiedi indicazioni al tuo supervisore prima di approvare.

## Far avanzare la segnalazione fino alla chiusura

Dopo l'approvazione la segnalazione passa da uno stato all'altro. Il pannello ti propone solo i passaggi permessi, quindi non
puoi sbagliare ordine. Il disegno completo degli stati è nella [Panoramica](panoramica.md).

**Assegnare un responsabile**

1. Premi **Assegna operatore**.
2. Scegli la persona dall'elenco **Operatore responsabile**.
3. Conferma. Il responsabile riceve la notifica "Nuova segnalazione assegnata" e la vede tra le sue segnalazioni.

**Cambiare stato**

1. Premi **Cambia stato**.
2. Scegli il nuovo stato tra quelli proposti.
3. Scrivi il **Motivo della modifica di stato**. Attenzione: il cittadino lo legge, quindi scrivi in modo semplice e
   cortese, senza nomi di colleghi o dati personali. Esempio: "Intervento programmato per lunedì 17 novembre".
4. Premi **Salva stato**.

| Da questo stato | Puoi passare a | Quando |
| --- | --- | --- |
| In revisione | In lavorazione, In sospeso | Il lavoro parte, oppure serve aspettare qualcuno |
| In lavorazione | In sospeso, Risolto | Il lavoro si blocca, oppure è finito |
| In sospeso | In lavorazione, Risolto | Il blocco è superato |
| Risolto | Riaperto, Chiuso | Il problema è tornato, oppure si archivia |
| Riaperto | In lavorazione | Si riprende il lavoro |
| Chiuso | - | Stato finale, non si modifica più |

**Se un collega ha appena cambiato la stessa segnalazione**

Se due persone lavorano sulla stessa segnalazione nello stesso momento, vale il primo che salva. Il secondo vede un errore
perché il passaggio non è più permesso dallo stato attuale: ricarica la pagina, leggi la cronologia e decidi di nuovo.

**Chiudere il lavoro**

1. Quando l'intervento è fatto, premi **Segna come risolta** e scrivi **Come è stata risolta**.
2. Il cittadino riceve l'aggiornamento e può valutare la soluzione.
3. Dopo qualche giorno, se il problema non si è ripresentato, premi **Chiudi pratica** e scrivi il motivo.

## Scrivere a uffici e fornitori

Spesso per risolvere serve un altro ufficio o una ditta esterna. FixCity tiene traccia di questi scambi nella cronologia
della segnalazione.

| Pulsante | A cosa serve | Cosa compilare |
| --- | --- | --- |
| **Invia comunicazione** | Mandare un'email, ad esempio alla ditta della manutenzione | Destinatario (facoltativo: una persona già assegnata alla pratica), Email destinatario, Oggetto, Messaggio |
| **Registra risposta ricevuta** | Annotare una risposta arrivata per telefono, email o di persona | Mittente e Sintesi della risposta. Non manda nessuna email |
| **Sollecita aggiornamento** | Ricordare a un ufficio o a una ditta che si aspetta una risposta | Email destinatario, Ufficio, Oggetto, Messaggio |

Esempio di uso:

1. La buca va chiusa dalla ditta esterna: premi **Invia comunicazione** e scrivi alla ditta.
2. La ditta risponde al telefono che interverrà giovedì: premi **Registra risposta ricevuta** e scrivi "Ditta Rossi:
   intervento giovedì".
3. Giovedì passa senza intervento: premi **Sollecita aggiornamento**.

## Errori comuni

| Errore | Conseguenza | Come evitarlo |
| --- | --- | --- |
| Approvare senza guardare le foto | Volti o targhe diventano pubblici | Apri ogni foto prima di **Approva** |
| Scrivere nomi di colleghi o dati personali nei motivi | Il cittadino li legge | Scrivi cosa succede, non chi lo fa |
| Lasciare segnalazioni In attesa per giorni | Il cittadino pensa di non essere ascoltato | Controlla le In attesa ogni mattina |
| Usare **Modifica** per cambiare lo stato | Non funziona: lo stato si cambia solo con le azioni | Usa **Cambia stato**, **Segna come risolta**, **Chiudi pratica** |
| Chiudere subito dopo la soluzione | Se il problema torna, una pratica chiusa non si riapre | Usa prima **Segna come risolta**, chiudi dopo qualche giorno |
| Cercare di cancellare una segnalazione | Solo l'amministratore può, e la cronologia pubblica si perde | Per una segnalazione non valida usa **Scarta** |

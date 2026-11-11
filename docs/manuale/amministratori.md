---
title: "FixCity - Guida per gli amministratori"
type: manual
audience: amministratori
updated: 2026-11-10
---

# Guida per gli amministratori

Questa guida è per chi gestisce gli accessi e i contenuti di FixCity per il Comune.

## Chi può fare cosa

Ogni persona riceve uno o più **ruoli**. I ruoli decidono cosa vede e cosa può fare.

| Cosa | Visitatore | Cittadino | Operatore | Supervisore | Amministratore | Super-amministratore |
| --- | --- | --- | --- | --- | --- | --- |
| Vedere mappa ed elenco pubblico | Sì | Sì | Sì | Sì | Sì | Sì |
| Inviare e seguire segnalazioni (con email confermata) | No | Sì | Sì | Sì | Sì | Sì |
| Entrare nel pannello `/fixcity/admin` | No | No | Sì\* | Sì\* | Sì\* | Sì\* |
| Approvare, scartare, assegnare, cambiare stato, comunicare | No | No | Sì | Sì | Sì | Sì |
| Cancellare una segnalazione | No | No | No | No | Sì | Sì |
| Gestire utenti, ruoli e contenuti delle pagine\* | No | No | No | No | Sì | Sì |
| Vedere il monitoraggio tecnico (Pulse) | No | No | No | No | No | Sì |

\* Serve anche il ruolo del pannello, per tutti compresi admin e super-admin: `fixcity::admin` per il pannello delle
segnalazioni. Ogni pannello ha il suo: `user::admin` per gli utenti, `cms::admin` per i contenuti, e così via.

| Ruolo | Nome tecnico da assegnare |
| --- | --- |
| Cittadino | Nessuno: lo diventa chi si registra |
| Operatore | `operator` + `fixcity::admin` |
| Supervisore | `supervisor` + `fixcity::admin` |
| Amministratore | `admin` + i ruoli `<pannello>::admin` dei pannelli che gestisce |
| Super-amministratore | `super-admin` |

Dai a ogni persona solo i ruoli che le servono. Il ruolo `admin` permette di cancellare segnalazioni e la loro cronologia
pubblica.

## Dare l'accesso a un nuovo operatore

Segui questi passi ogni volta che un nuovo dipendente deve gestire le segnalazioni.

1. **Fai creare l'account.** Chiedi alla persona di registrarsi dal sito con la sua email di lavoro e di confermarla con il
   collegamento che riceve (guida per i [cittadini](cittadini.md), "Creare l'account"). In alternativa crea tu l'utente dal
   pannello utenti.
2. **Apri il pannello utenti.** Vai su `/user/admin` ed entra con il tuo account.
3. **Trova la persona.** Nel menu apri **Utenti** e cerca il nome o l'email.
4. **Assegna i ruoli.** Apri la scheda dell'utente, vai alla parte **Ruoli** e aggiungi `operator` e `fixcity::admin`. Per un
   coordinatore usa `supervisor` al posto di `operator`.
5. **Salva.**
6. **Comunica l'indirizzo** del pannello: `/fixcity/admin`, e la [guida per gli operatori](operatori.md).

**Togliere l'accesso** quando qualcuno cambia ufficio o lascia il Comune: apri la sua scheda utente e rimuovi `operator` e
`fixcity::admin`. Le segnalazioni già assegnate a lui vanno riassegnate a un collega.

**Account cancellati.** Quando un cittadino elimina il proprio account, l'accesso si chiude subito ovunque: sito, pannelli,
app e collegamenti già aperti. Non serve nessuna azione da parte tua.

Le schermate del pannello verranno aggiunte dopo il prossimo rilascio.

## Modificare i contenuti delle pagine

Testi della pagina iniziale, novità, eventi e pagine informative si gestiscono dal pannello contenuti, all'indirizzo
`/cms/admin`. Serve il ruolo `cms::admin`.

1. Apri `/cms/admin` ed entra.
2. Scegli il tipo di contenuto: pagine, novità o eventi.
3. Apri il contenuto da cambiare oppure creane uno nuovo.
4. Scrivi il testo in ogni lingua del sito: italiano, inglese, tedesco e spagnolo. Se una lingua resta vuota, chi la usa vede
   il testo italiano.
5. Salva e apri la pagina pubblica per controllare il risultato.

Le pagine con informativa privacy, note legali e dichiarazione di accessibilità contengono dati dell'ente che si cambiano
solo con il fornitore: vedi la sezione successiva.

## Cosa si cambia solo con il fornitore

Alcune impostazioni oggi non sono nel pannello. Per cambiarle scrivi al fornitore di FixCity con le informazioni indicate.

| Cosa | Cosa mandare al fornitore |
| --- | --- |
| Dati dell'ente: nome, indirizzo, PEC, contatti | I dati ufficiali del Comune |
| Responsabile della protezione dei dati (DPO) | Nome e contatti del DPO |
| Informativa privacy | Il testo approvato dal Comune |
| Dichiarazione di accessibilità | Il testo e il link alla dichiarazione pubblicata su AgID |
| Elenco dei tipi di segnalazione | L'elenco delle voci da aggiungere, togliere o rinominare |
| Accesso con Google | La richiesta di attivazione |
| Dominio del sito | Il nome di dominio scelto |

**Prima di aprire il servizio ai cittadini** controlla che informativa privacy e dichiarazione di accessibilità non
contengano segnaposto come `[[ente.name]]`: vuol dire che i dati dell'ente non sono ancora stati inseriti. Finché
l'informativa ha segnaposto, **FixCity rifiuta l'invio di nuove segnalazioni** e mostra ai cittadini il messaggio "Il Comune
non ha ancora completato i propri dati nell'informativa privacy".

## Super-amministratore

Il super-amministratore è di solito il tecnico del fornitore, non un dipendente del Comune.

- Ha tutti i permessi dell'amministratore, sempre insieme ai ruoli dei pannelli che usa.
- È l'unico che vede il **monitoraggio tecnico** (Pulse): carico del server, richieste lente, errori.
- Non va usato per il lavoro di tutti i giorni sulle segnalazioni: per quello serve un account da operatore.

Tieni il numero di super-amministratori al minimo, uno o due, e rimuovi subito il ruolo a chi non ne ha più bisogno.

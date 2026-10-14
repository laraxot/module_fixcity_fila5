---
id: dcp-154
title: "DCP-154: gate di messa in produzione, un solo comando che dice se si puo' pubblicare"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "gate messa in produzione go-live publication-gate production-checklist https debugbar app_debug mail from secure cookie csp"
related: [dcp-112, dcp-130, dcp-132, dcp-133, dcp-139, dcp-153]
---
# DCP-154: gate di messa in produzione, un solo comando che dice se si puo' pubblicare

## Lacuna e prova (2026-10-14)
I controlli che separano «funziona in sviluppo» da «si puo' pubblicare» esistono ma sono sparsi, e nessuno li lancia tutti:
- Guardie di pubblicazione rosse per costruzione: `PUBLICATION_GATE=1 pest --group=publication-gate` (informativa privacy DCP-130, dichiarazione di accessibilita' DCP-132). Senza la variabile risultano **saltate**, non fallite.
- `bashscripts/tools/security/production-checklist.sh <https://origine>` (DCP-139): configurazione e risposta HTTP reale.
- Guardia sui cookie (DCP-153) e struttura delle pagine nelle 4 lingue (`PublicPagesStructureTest`).
- Configurazione di questo ambiente, letta da `laravel/.env` il 2026-10-14: `APP_ENV=local`, `APP_DEBUG=true`, `DEBUGBAR_ENABLED=true`, `APP_URL=http://fixcity.local`, `MAIL_FROM_ADDRESS=noreply@fixcity.local`, `MAIL_MAILER=sendmail`. `.env.production.example` ha gia' `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`.
- La geolocalizzazione del dispositivo richiede un contesto sicuro: su HTTP il browser la blocca senza chiedere (matrice I-25, NON VERIFICATO in produzione).

## Norma
Matrice normativa N-14 (GDPR art. 32, trasporto e intestazioni), N-13; matrice interfaccia I-23 (nessuno strumento di sviluppo in produzione), I-25 (HTTPS e geolocalizzazione). Per le guardie: DCP-130, DCP-132.

## Requisiti
1. Un solo comando (`python3 -I bashscripts/tools/release/go-live-gate.py --base <https://origine> --env-file <.env di destinazione>`) che esegue, in ordine: le guardie di pubblicazione con `PUBLICATION_GATE=1`, `production-checklist.sh`, i test dei cookie e della struttura delle pagine, e i controlli di ambiente sotto.
2. Controlli di ambiente sull'`.env` di destinazione: `APP_ENV=production`, `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`, `APP_URL` con `https://` e senza `.local`, `MAIL_FROM_ADDRESS` senza `.local` e sul dominio dell'ente (vedi DCP-155 per SPF, DKIM e DMARC).
3. Esito riassunto: elenco di cio' che manca **con il nome del responsabile** (l'ente per i testi e i recapiti, il fornitore per l'hosting). Una guardia saltata e' un FAIL, non un PASS.
4. Nessuna credenziale in output.

## Criteri di accettazione
- [ ] Su questo ambiente il comando fallisce e dice perche' (almeno `APP_DEBUG`, `DEBUGBAR_ENABLED`, `APP_URL`, `MAIL_FROM_ADDRESS`, informativa incompleta).
- [ ] Su un ambiente di prova configurato come produzione, con i testi dell'ente forniti, esce con 0.
- [ ] Una guardia saltata fa fallire il gate.

## Dipendenze e priorita'
DCP-130 e DCP-132 (le guardie), DCP-133 (identita' dell'ente), DCP-139 (checklist), DCP-153 (cookie). P0: senza questo gate la messa in produzione si decide a occhio.

## Stato al 2026-10-14 (verificato)
Implementato `bashscripts/tools/release/go-live-gate.py` (self-test, `--skip-pest`, `--strict`, `--json`; solo GET, nessuna credenziale in output). Su `http://127.0.0.1:8081` con il `.env` locale: NON pubblicabile, 10 bloccanti falliti, 7 requisiti delle Linee guida, 9 superati su 29 (prima dei controlli ENV-09). Su `laravel/.env.production.example` (`--skip-pest`): 6 bloccanti, tutti dovuti a dati che il progetto non possiede (dominio reale DCP-155, testi dell'ente DCP-130/132/162, HTTPS DCP-139).
Difetti trovati usando il gate e corretti: (1) `LEG-01` passava a Pest percorsi relativi alla root invece che a `laravel/` e leggeva «esito non leggibile»; (2) `LG-01` mostrava «?» quando lo scan omette la riga a zero; (3) `.env.production.example` impostava `MAIL_DRIVER` (chiave ignorata: `config/mail.php` legge `MAIL_MAILER`, default `log`, quindi la posta in produzione sarebbe finita nel log) e non aveva `MAIL_FROM_ADDRESS`; (4) lo stesso file impostava `LOG_CHANNEL=sentry` in coda, ma `sentry/sentry-laravel` non e' in `composer.json` e `config/logging.php` non ha il canale: ora `ENV-09` lo segnala e la riga e' commentata.
Aggiunti dopo: `ENV-09` (canale di log definito in `config/logging.php`) e `LEG-04` (informativa, condizioni, note legali, accessibilita' e cookie policy senza l'avviso di testo incompleto: 12 pagine su 20 oggi, perche' la registrazione fa accettare testi che non esistono ancora).
Non verificato: il comando non prova che la posta arrivi, che HTTPS sia valido (richiede un'origine https), ne' i bloccanti del lato ente.

## Checklist operativa di rilascio (dal plugin engineering, adattata)
Il gate controlla configurazione e contenuti. Mancano, e non sono automatizzabili da qui, queste voci (responsabile: fornitore/sviluppo). Non risultano nel repository: pacchetto di backup (`spatie/laravel-backup` assente da `composer.json`), attivita' pianificate in `routes/console.php`, procedura di rollback.
- [ ] Backup del database e dei file caricati con un ripristino provato almeno una volta (fornitore). 
- [ ] `php artisan migrate --pretend` sul database di destinazione prima del rilascio; copia di sicurezza subito prima.
- [ ] Piano di rollback scritto: versione precedente, come tornarci, cosa succede alle migrazioni gia' eseguite (Git forward-only: si rilascia una correzione, non si torna indietro con `reset`).
- [ ] Worker delle code (`QUEUE_CONNECTION=redis` nell'esempio di produzione) e cron dello scheduler attivi e monitorati; oggi in sviluppo `QUEUE_CONNECTION=sync`.
- [ ] `php artisan config:cache route:cache view:cache` dopo il deploy (la memoria sul cache di config spiega perche' i flag di tenant e le intestazioni lo richiedono).
- [ ] Prova rapida dopo il deploy: le home in 4 lingue, `/it/tickets/track`, invio di una segnalazione di prova con utente @example.test, arrivo della email di conferma, cookiebar visibile.
- [ ] Monitoraggio dei primi 15 minuti (errori 5xx, log) e soglie di rollback decise prima: 5xx oltre una soglia, wizard che non arriva al riepilogo, posta non recapitata.
- [ ] Note di rilascio e avviso al titolare del trattamento.

## Cosa serve per pubblicare, per responsabile (2026-10-14, sera)
Misura: `python3 -I bashscripts/tools/release/go-live-gate.py --skip-pest` su `http://127.0.0.1:8081`: **11 bloccanti, 3 requisiti LG, 15 controlli superati su 32**. Il lavoro che spettava allo sviluppo e si poteva fare senza dati dell'ente e' stato fatto (sitemap, canonical e hreflang, traduzioni dei titoli, registrazione a norma con prova dei consensi, cookiebar, audit di sicurezza dei pannelli e correzioni, assenza di marker di merge: `REPO-01` verde). Quello che resta non e' codice:
| Responsabile | Cosa | Controlli |
|---|---|---|
| **Ente (titolare)** | Denominazione, regione, recapiti, sede, codice fiscale al posto dei segnaposto («Il mio Comune», «Nome della Regione», «1234567»...) nelle home in 4 lingue | LEG-02 |
| **Ente (titolare)** | Testi di informativa privacy, condizioni d'uso, note legali, dichiarazione di accessibilita' (le guardie `PUBLICATION_GATE=1` sono rosse per costruzione finche' mancano) | LEG-04, LEG-01 |
| **Ente (DPO)** | Contatto del DPO raggiungibile dalla home e dalle pagine legali (`DCP-164`); decisioni D1-D7 su registro delle richieste, cancellazione, verifica dell'email, minori (`DCP-140`, `DCP-165`, `DCP-178`) | LG-02 |
| **Titolare e fornitore** | Nome a dominio definitivo (`DCP-155`), indirizzo `APP_URL` con `https`, mittente della posta sul dominio del servizio | ENV-04, ENV-05 |
| **Fornitore** | `.env` di produzione: `APP_ENV=production`, `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`; HTTPS valido e controlli `production-checklist.sh`; posta che arriva davvero (SMTP, non `sendmail`); backup con ripristino provato, worker delle code e scheduler, intestazioni di cache lunghe per `/themes/Sixteen/` (vedi DCP-183); **cambiare la password FTP del disco `photos` di prova, che resta nella storia di git** | ENV-01/02/03/06, NET-01, ENV-08 |
Non bloccanti ma richiesti dalle Linee guida: data di ultimo aggiornamento su ogni pagina (`DCP-167`, serve la regola su quale data), valutazione della pagina (`DCP-169`, serve dove memorizzare), licenza dei contenuti (`DCP-170`, decisione dell'ente), `security.txt` (serve un contatto per le segnalazioni di sicurezza), ricerca interna (campo che non risponde: collegarla o toglierla).


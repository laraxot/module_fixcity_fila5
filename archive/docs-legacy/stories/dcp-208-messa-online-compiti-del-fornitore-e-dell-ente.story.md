---
id: dcp-208
title: "DCP-208: messa online: cosa deve fare il fornitore e cosa deve consegnare l'ente"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "messa online fornitore ente env produzione https posta smtp backup worker scheduler cache header debugbar gate go-live"
related: [dcp-154, dcp-155, dcp-200, dcp-202]
tags: [go-live, operativita, fornitore, ente]
---

# DCP-208: messa online: cosa deve fare il fornitore e cosa deve consegnare l'ente

## Fase BMAD
Plan. Il gate e' `bashscripts/tools/release/go-live-gate.py` (ultima misura: 11 bloccanti, 3 requisiti LG, 15 superati su 32, a cui si aggiunge `REPO-02` sui nomi dei `.md` finche' DCP-201 non e' chiusa). Qui sta l'elenco operativo per chi non e' lo sviluppo; il dettaglio dei controlli e' in DCP-154.

## Da consegnare dall'ente
| Cosa | Controllo | Decisione |
|---|---|---|
| Denominazione, regione, sede, recapiti, codice fiscale, numero verde veri (oggi segnaposto) | LEG-02 | DEC-01 |
| Testi di informativa, condizioni d'uso, note legali, media policy, dichiarazione di accessibilita' | LEG-04, LEG-01 | DEC-02 |
| Nome del DPO e recapito | LG-02 | DEC-06 |
| Indirizzi dei social, oppure la conferma che si tolgono | - | DEC-03 |
| Caselle: feedback accessibilita' e segnalazioni di sicurezza | - | DEC-04 |

## Da fare dal fornitore
- [ ] `.env` di produzione: `APP_ENV=production`, `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`, `APP_URL` con `https` (ENV-01..06, NET-01; DEC-25). Il modello e' `.env.production.example`, corretto in DCP-154.
- [ ] HTTPS valido e `production-checklist.sh`; elenco degli host fidati (`trustHosts`) per evitare l'header `Host` nella sitemap.
- [ ] Posta che arriva davvero: SMTP (non `sendmail`), mittente sul dominio del servizio, SPF e DKIM (ENV-05, ENV-08; DEC-26).
- [ ] Backup con **ripristino provato** (allegare la prova), worker delle code, scheduler (DEC-27).
- [ ] Intestazioni di cache lunghe per `/themes/Sixteen/` (oggi `public_html/.htaccess` senza `Cache-Control`; in sviluppo `max-age=3600`); misurare con un server vero, non con `php -S`.
- [ ] Il web server nega l'esecuzione di `.php` sotto `/storage` e `/uploads` (prova da allegare).
- [ ] Cambiare la password FTP del disco `photos` e la chiave Supermemory (DEC-28).
- [ ] `public_html/info.php` non esiste (rimosso oggi): verificare `GET /info.php` = 404 in produzione.

## Decisioni del titolare
Nome e dominio (DEC-16): condizionano `APP_URL`, la posta, la sitemap e la riga `Sitemap:` di `robots.txt`.

## Criteri di accettazione
- [ ] `go-live-gate.py --base https://<dominio>` senza bloccanti; l'esito incollato in DCP-154 con data e chi l'ha eseguito.
- [ ] Ogni evidenza (ripristino del backup, test della posta, verifica HTTPS) allegata o linkata, non dichiarata.

## Non verificato
Tutto cio' che richiede la produzione: HTTPS, header, assenza di Debugbar, Apache, posta reale, backup.

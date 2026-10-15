---
id: dcp-155
title: "DCP-155: dominio, DNS, posta e identita' di rete del servizio"
type: story
epic: dc-parity-v2
status: needs-decision
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "dominio nome dominio fixcity.eu fixcity.it segnalacitta civicfix registrar registro .it spf dkim dmarc tls hsts security.txt marchio"
related: [dcp-112, dcp-133, dcp-139, dcp-154]
---
# DCP-155: dominio, DNS, posta e identita' di rete del servizio

## Lacuna e prova (2026-10-14, verifiche di rete eseguite da questa macchina)
Il progetto non ha un dominio di produzione deciso: `APP_URL=http://fixcity.local`, mittente `noreply@fixcity.local`, tenant `config/local/fixcity` e `config/eu/fixcity`.

Disponibilita' del nome «FixCity» (whois sulla porta 43 per `.it` e `.eu`, RDAP per gli altri; stato «registrato» o «non registrato», da riconfermare al momento dell'acquisto):
| Dominio | Stato | Che cosa risponde |
|---|---|---|
| `fixcity.it` | registrato | un sito con lo stesso nome che invita a «segnalare i rifiuti abbandonati nella tua zona»: servizio simile, stesso mercato |
| `fixcity.eu` | registrato | risponde con un'applicazione Laravel (cookie `laravel_session`, redirect a `/it`) su hosting Hostinger; registrar Realtime Register, organizzazione indicata «HOSTINGER operations, UAB». Il repository ha il tenant `config/eu/fixcity`: con ogni probabilita' e' il dominio del progetto, ma **la titolarita' non e' dimostrata da qui**: va verificata nell'account del fornitore |
| `fixcity.com`, `.org`, `.net`, `.app`, `.city` | registrati | `.city` ospita un servizio di segnalazione con altro nome («CityFix», in arabo) |
| `fixcity.io` | non risulta registrato (RDAP 404) | non verificato oltre l'RDAP |

Nomi liberi al 2026-10-14: `segnalacitta.it`, `.com`, `.eu`, `.org`, `.net`, `.app`, `.city`; `cittafix.it`, `.com`, `.eu`, `.org`; `civicfix.it`, `.eu`, `.app` (`civicfix.com` e' registrato); `fixacitta.it`, `fixcomune.it`, `comunefix.it`, `fixmycomune.it`, `riparacitta.it`, `fixlacitta.it`, `fixcivico.it`, `fixpiazza.it`, `fixurbe.it`.

Non verificato: marchi (banche dati UIBM ed EUIPO non interrogate), prezzi, regole di assegnazione `.it` lette sul sito del Registro (la pagina non e' raggiungibile; le condizioni arrivano da rivenditori), nome di dominio che l'ente usera' per il proprio servizio.

## Norma
GDPR art. 13 par. 1 lett. a) (il sito dice chi e' il titolare) e matrice N-30, I-15; N-14 (HTTPS e HSTS). Per la posta, nessuna norma qui, ma senza SPF, DKIM e DMARC le email del servizio (conferme, notifiche, modulo di accessibilita') finiscono nello spam. Non verificato: indicazioni AgID sui domini delle PA (`.gov.it`): da leggere prima di proporre un sottodominio a un Comune.

## Requisiti
1. **Decisione**: tenere il marchio FixCity oppure cambiarlo. Con `fixcity.it` in mano a un servizio simile si rischia confusione in Italia: serve una ricerca di marchio (UIBM, EUIPO) prima di investire nel nome.
2. Se si cambia, candidato proposto: **SegnalaCitta** (`segnalacitta.it` per i cittadini, `segnalacitta.eu` e `.com` difensivi): descrive l'azione che il cittadino compie, si legge in italiano senza spiegazioni. Alternativa internazionale: **CivicFix** (`civicfix.eu`, `civicfix.it`).
3. Se si tiene FixCity: confermare la titolarita' di `fixcity.eu` nell'account del fornitore, attivare il rinnovo automatico e il blocco di trasferimento.
4. Acquisto: `.it` solo tramite un registrar accreditato presso il Registro `.it` (il registrante deve essere residente o stabilito nello spazio economico europeo secondo i rivenditori: confermare sul sito del Registro); `.eu` tramite un registrar accreditato EURid; per gli altri, un registrar a scelta con RDAP pubblico. Confrontare i prezzi di rinnovo, non solo del primo anno.
5. Per ogni Comune: un sottodominio del Comune (`segnalazioni.<comune>.it`) o un dominio della piattaforma con il tenant davanti: decisione da prendere con l'ente.
6. Configurazione: TLS valido con rinnovo automatico, HSTS (DCP-139), `MAIL_FROM_ADDRESS` sul dominio, record SPF, DKIM e DMARC, `/.well-known/security.txt` con il contatto per le segnalazioni di sicurezza, `robots.txt` e `sitemap.xml` coerenti con le pagine pubbliche, `<link rel="canonical">`.

## Criteri di accettazione
- [ ] Decisione scritta su nome e dominio, con l'esito della ricerca di marchio.
- [ ] Titolarita' e rinnovo del dominio scelto verificati; `dig` mostra SPF, DKIM e DMARC; una email di prova arriva in casella (non in spam) con le intestazioni che li superano.
- [ ] `curl -sI https://<dominio>` mostra HSTS e il certificato valido; `security.txt` risponde 200.

## Dipendenze e priorita'
Decisione dell'utente (nome e marchio); DCP-133 (identita' dell'ente); DCP-154 (il gate controlla l'URL e il mittente). P1: non blocca lo sviluppo, blocca la pubblicazione.

---
id: dcp-139
title: "DCP-139: intestazioni di sicurezza HTTP e hardening per la produzione"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P1
owner: app:laravel + module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "security headers csp hsts x-frame-options nosniff x-powered-by securityheaders middleware permissions-policy geolocation debugbar produzione tls"
related: [dcp-112, dcp-138, dcp-137]
---

# DCP-139: intestazioni di sicurezza attive e ambiente di produzione verificato

Nata da DCP-112 (matrice normativa N-14).

## Lacuna e prova (2026-10-13)
- `curl -sI http://192.168.1.40:8000/it/auth/login` e `/it/tickets`: nessun `Content-Security-Policy`, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Strict-Transport-Security`; presente `X-Powered-By: PHP/8.4.26`.
- `laravel/app/Http/Middleware/SecurityHeaders.php` esiste ma non e' registrato da nessuna parte (grep). Se lo fosse cosi' com'e', `Permissions-Policy: geolocation=()` (riga 57) disattiverebbe il pulsante «Usa la tua posizione» del passo 2, e la CSP `script-src 'self' 'unsafe-inline' https:` e' molto permissiva.
- Il vhost `laravel/config/vhost/fixcity.local-ssl.conf` imposta `nosniff` e `X-Frame-Options SAMEORIGIN`; HSTS e' commentato; nessuna CSP.
- `.env` locale: `APP_DEBUG=true`, `DEBUGBAR_ENABLED=true`; `.env.production.example` ha `false` per entrambi. Il proxy TLS `https://192.168.1.40:8080` non ha risposto: HTTPS non verificato.

## Norma
GDPR art. 32 (sicurezza del trattamento, testo nel registro fonti 02). Per un ente soggetto alle misure minime AgID (circolare 2/2017) o a NIS2 valgono i controlli del rispettivo ambito (da verificare con l'ente, N-20..N-22).

## Requisiti
1. Registrare un solo middleware di intestazioni (riusare `SecurityHeaders`, correggendo `geolocation=(self)`), con CSP elaborata sul risultato di DCP-137 (host reali) e test con Livewire, Filament e Alpine.
2. HSTS solo su HTTPS; rimuovere `X-Powered-By` (`expose_php=Off` o middleware).
3. Lista di controllo di produzione eseguibile: `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`, cookie `Secure`, redirect a HTTPS; verifica su ambiente con TLS.
4. Test d'integrazione che legge le intestazioni di tre rotte e fallisce se ne manca una.

## Criteri di accettazione
- [ ] `curl -sI` su `/it`, `/it/auth/login`, `/it/tickets`: presenti CSP, `X-Frame-Options` o `frame-ancestors`, `nosniff`, `Referrer-Policy`; assente `X-Powered-By`.
- [ ] La geolocalizzazione del passo 2 funziona con le intestazioni attive (Playwright con permesso concesso).
- [ ] Nessun errore CSP nella console su home, login, elenco, wizard.
- [ ] Risultato della lista di controllo allegato alla story con data e ambiente.

## Dipendenze e priorita'
DCP-137 (host terzi), DCP-138. P1.

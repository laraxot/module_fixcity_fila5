---
title: "Architecture — contenuto partner nel footer"
type: architecture
status: proposed
updated: 2026-10-15
tags: [bmad, architecture, cms, filament, footer, multi-tenant]
qmd: "architecture decision record fixcity"
---

# Architecture decision record

## Confine

Sixteen resta il vestito del footer. Il contenuto vive nel record CMS della sezione
footer del tenant e viene letto tramite `<x-section slug="footer">`. Filament
Blueprint è il futuro punto di gestione editoriale, non una nuova fonte parallela.

## Modello concettuale

`Sponsor/Partner domain → approved public projection → FooterSection support_block`

Il JSON del footer è una proiezione di rendering, non l'unico archivio autorevole
per contratti, approvazioni, scadenze o diritti commerciali. Per il pilota statico
può bastare il CMS esistente; un catalogo sponsor contrattuale richiederebbe invece
un dominio separato e una decisione tenant esplicita.

Campi minimi di `PartnerEntry`: `name`, `logo`, `alt`, `url`, `relationship`,
`disclosure`, `locale`, `tenant`, `starts_at`, `ends_at`, `status`, `reviewed_by`,
`reviewed_at`. Nessun dato comportamentale dell'utente.

## Scelte

- JSON/CMS come source of truth per copy e ordine.
- Filament come interfaccia di validazione/pubblicazione, con policy e permessi.
- Blade responsabile solo di struttura, semantica, escaping, focus e responsive.
- URL interni localizzati; URL esterni marcati e aperti in modo esplicito.
- Asset logo locali o CDN controllato, mai HTML/script fornito dallo sponsor.
- Stato `draft → review → approved → published → expired/revoked`.

## Rifiuti

- Ad server e banner HTML arbitrario: coupling, tracking e rischio sicurezza.
- Config globale per sponsor: rompe tenant isolation.
- Loghi hardcoded nella Blade: duplica contenuto e rende impossibile la governance.
- Nuovo modulo sponsor prima della validazione: YAGNI.

## Filament Blueprint futuro

Il blueprint dovrà definire campi, policy, preview accessibile, validazione MIME/
dimensione, controllo URL, ruolo approvatore, audit e scadenza. Non deve consentire
di pubblicare senza disclosure o alt text. Questo documento non autorizza ancora la
creazione della resource.

## NFR e failure mode

- Record mancante: nessuna sezione inventata; il footer resta usabile.
- URL non valido: partner non pubblicabile.
- Logo mancante: errore editoriale, non fallback a immagine generica.
- Scadenza: rimozione dalla pubblicazione senza cancellare l'audit.
- Tenant non autorizzato: nessun contenuto cross-tenant.

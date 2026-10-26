---
title: "Domain research — disclaimer, termini e pagine legali FixCity"
type: research-report
status: complete-for-planning
updated: 2026-10-14
tags: [bmad, domain-research, legal-content, disclaimer, gdpr, multilingual]
qmd: "executive summary fixcity"
---

# Executive Summary

FixCity ha già un buon meccanismo tecnico per pubblicare privacy policy, cookie
policy, note legali e dichiarazione di accessibilità per tenant e lingua. Il
problema principale non è aggiungere altro testo generico: i contenuti del tenant
sono ancora segnaposto o incompleti e non esiste una pagina esplicita per termini
di utilizzo/disclaimer.

Le fonti consultate convergono su una separazione necessaria: privacy policy e
cookie policy descrivono trattamenti e tecnologie; termini/disclaimer regolano uso
del servizio, responsabilità, contenuti di terzi, proprietà intellettuale e
limitazioni lecite. Nessuna delle fonti commerciali è una validazione legale per il
Comune: il testo deve essere fornito e approvato dal titolare/DPO/legale.

# Research Scope

## In scope

- Disclaimer e termini d'uso per un portale civico multilingua.
- Identità del titolare, proprietà dei contenuti, link esterni, UGC/commenti,
  disponibilità del servizio e limiti di responsabilità.
- Confronto con privacy, cookie e accessibilità già presenti in FixCity.

## Out of scope

- Redazione di clausole legali definitive.
- Scelta della base giuridica o dei tempi di conservazione.
- Certificazione GDPR, AGID o consulenza professionale.

# Methodology and Sources

Analisi dei tre URL forniti, controllo del progetto e confronto con le pagine
legali già implementate. Accesso effettuato il 2026-10-14.

| Source | Result | Use |
|---|---|---|
| https://www.termsfeed.com/it/dichiarazione-liberatoria-generatore/ | Cloudflare challenge; contenuto non verificabile direttamente | Non usata per affermazioni sostanziali |
| https://sos-wp.it/digital-marketing/come-scrivere-un-disclaimer/ | Accessibile; articolo aggiornato 2022 | Checklist orientativa: disclaimer separato, privacy, cookie, titolare, dati e servizi terzi |
| https://www.servizindustria.it/termini-di-utilizzo-e-disclaimer-blog/ | Accessibile; pagina aggiornata 2023 | Esempio strutturale: uso del sito, link terzi, proprietà intellettuale, copyright, UGC/commenti, responsabilità |
| Google Ads URL fornito | Redirect pubblicitario a Iubenda | Non fonte normativa indipendente; non usata come prova |

Le fonti SOS WP e Servizi Industria sono articoli/esempi editoriali, non fonti
normative. Sono quindi input di requisiti e non testo da riutilizzare.

# Findings

## What the external material implies for FixCity

1. **Separate documents.** Privacy/cookie information cannot be replaced by a
   generic disclaimer. The user needs separate links and a clear scope for each.
2. **Publisher identity.** Terms/legal notes need tenant identity, contact channel,
   legal address and any applicable registration/tax details supplied by the owner.
3. **Service scope.** Terms should explain the reporting service, account rules,
   acceptable use, abuse, security, interruptions, and that the portal is not a
   substitute for emergency channels where applicable.
4. **Third-party boundaries.** External links, maps, identity providers, mail,
   storage and other providers need ownership/responsibility wording and privacy
   cross-links.
5. **Content and media.** The portal receives descriptions, locations, images and
   comments. Rules for lawful uploads, third-party rights, moderation/removal and
   public visibility must be explicit and consistent with the privacy/media policy.
6. **Liability limits need legal drafting.** Generic “as-is” or blanket exclusions
   cannot be copied; mandatory consumer/public-administration responsibilities and
   non-excludable liability must be preserved.
7. **Versioning and acceptance.** Terms need publication date, last update,
   language version and, where acceptance is required, a recorded version/hash and
   timestamp. Cookie consent must remain separate from acceptance of service terms.

## What exists in FixCity

- JSON content pages exist for `privacy.index`, `note-legali.index`,
  `cookie-policy.index`, `media-policy.index` and `accessibilita.index` in the
  Fixcity configuration.
- Privacy publication is guarded by `AssessPrivacyPolicyAction`: ten GDPR art. 13
  sections are required and placeholders are rejected.
- Accessibility publication is guarded by
  `AssessAccessibilityStatementAction`; the tenant declaration currently contains
  null/empty values in the inspected local/EU configurations.
- `policy.md` in the inspected EU configuration is still the generic
  “Edit this file” template. The local Italian policy is a short operational notice,
  not a complete publication-gate document with all required sections.
- No tenant `legal-notes.md` or `media-policy.md` content was found in the inspected
  `config/` tree. The pages therefore have a technical shell but no approved text.
- Terms files are also generic “Edit this file” templates. A dedicated public
  disclaimer/terms content page and a complete publication gate are not evident.
- Several Sixteen templates still contain `#`, hard-coded legal paths or inconsistent
  terms/privacy links; this remains a technical story, not a legal-copy task.

# Gaps and Opportunities

## Designers Italia: findings relevant to FixCity

The supplied Docs Italia page explicitly marks that historical page as superseded
and points to the current Designers Italia design guidelines and operational manual.
It remains useful as a map of the required design disciplines: citizen-centred
service design, explicit users/stakeholders and measurable indicators, user stories
and prototypes, continuous improvement, information architecture, accessibility,
plain language, SEO, qualitative research/usability testing, analytics, common UI
kits, and privacy by design/GDPR including information notices and cookies. It also
describes versioned releases, so a compliance claim must identify the reference
version rather than saying only “Design Comuni compliant”.

For FixCity, Bootstrap Italia is a visual/semantic reference only. The runtime
implementation remains TailwindCSS + Alpine.js + Lit + DaisyUI + Flowbite, with no
Bootstrap runtime, and the new stories must connect to the existing cookie/Gdpr and
legal streams without duplicating them.

| ID | Gap | Risk | Planned BMAD story |
|---|---|---|---|
| LEG-01 | Terms/disclaimer document and route/content contract absent or incomplete | Users cannot see service rules and owner cannot approve one canonical version | 6.12 |
| LEG-02 | Tenant legal identity, copyright, content/media, external-link and UGC fields not modelled as an approval checklist | Copy can omit facts needed for a municipal portal | 6.13 |
| LEG-03 | Terms/privacy acceptance links and recorded version semantics are inconsistent | Acceptance may point to `#` or prove the wrong document/version | 6.14 |
| LEG-04 | EU/local legal publication gates are incomplete | Release could expose template or incomplete legal content | 6.15 |
| LEG-05 | Previous route audit claimed legal routes were absent; source tests/config prove the CMS/Folio routes are implemented | Planning could duplicate routes and create conflicts | Corrected in audit/Second Brain |

| DESIGN-01 | Current design-guideline baseline/version is not pinned in release evidence | A parity claim can refer to a superseded or ambiguous source | New Epic 8 stories |
| DESIGN-02 | Service goals, stakeholders, user research and measurable outcomes are not consistently evidenced | UI can look compliant while failing the actual citizen task | New Epic 8 stories |
| DESIGN-03 | IA, content/plain-language, SEO, accessibility and analytics checks are not one traceable gate | Important design disciplines remain informal | New Epic 8 stories |
| DESIGN-04 | Governance, service blueprint, content rights, prototyping, component contracts and procurement evidence are not consolidated | Delivery can pass a visual review while failing service or supplier obligations | 8.9–8.18 |

## Current normative baseline

The historical introduction above is superseded. Epic 8 uses the current
requirements page and the operational manual as its references:

- Manuale operativo: https://docs.italia.it/italia/designers-italia/manuale-operativo-design-docs/
- Requisiti correnti: https://docs.italia.it/italia/design/lg-design-servizi-web/it/versione-corrente/requisiti.html

The current requirements are tracked as 4.1 accessibilità, 4.2 affidabilità/
trasparenza/sicurezza, 4.3 semplicità ed esperienza, 4.4 monitoraggio, 4.5
interfaccia, 4.6 piattaforme abilitanti, 4.7 licenze and 4.8 attuazione. The
version/date and evidence state are pinned by 8.1 and consolidated by 8.18.

# Recommendations for BMAD

1. Keep legal copy owner-supplied. Add schema/checklist and publication gates, not
   invented clauses.
2. Add one canonical localized “Termini e disclaimer” content contract, with
   explicit document version and publication metadata.
3. Link registration and report submission to the correct document without merging
   terms acceptance with cookie consent or GDPR treatment consent.
4. Add owner/DPO approval evidence to the release gate for every enabled locale and
   tenant.
5. Use the existing Fixcity Actions and JSON/CMS page pattern; do not introduce a
   parallel legal-page subsystem.
6. Treat Designers Italia as an end-to-end service-design system, not just a CSS
   component library; pin the current reference before release and record evidence
   for research, IA, content, accessibility, privacy and measurement.
7. Complete the deeper operational gaps through 8.9–8.18: governance, service
   ecosystem, content rights/localization, interaction validation, component
   contracts, reliability/security, platform applicability, licensing, feedback
   and the 4.1–4.8 procurement gate.

# Limitations

This is domain research and code inventory, not legal advice. The supplied
TermsFeed page was blocked by Cloudflare, and no municipality identity, DPO,
emergency-channel policy, data-retention decision or final legal copy was available.

# Bibliography

- TermsFeed, “Dichiarazione liberatoria generatore”,
  https://www.termsfeed.com/it/dichiarazione-liberatoria-generatore/ (accessed
  2026-10-14; Cloudflare challenge).
- SOS WP, “Come scrivere un disclaimer per il tuo sito web”,
  https://sos-wp.it/digital-marketing/come-scrivere-un-disclaimer/ (accessed
  2026-10-14; article metadata shows update 2022-10-20).
- Servizi Industria, “Termini di utilizzo e Disclaimer Blog”,
  https://www.servizindustria.it/termini-di-utilizzo-e-disclaimer-blog/ (accessed
  2026-10-14; page metadata shows update 2023-10-20).
- Google Ads/Iubenda redirect supplied by the user (accessed 2026-10-14). Treated
  as advertising, not an independent legal source.
- Designers Italia / Docs Italia, “Introduzione alle linee guida di design” (accessed
  2026-10-14),
  https://docs.italia.it/italia/designers-italia/design-linee-guida-docs/it/stabile/doc/introduzione-linee-guida-design.html
- Designers Italia / Docs Italia, “Manuale operativo” (accessed 2026-10-14),
  https://docs.italia.it/italia/designers-italia/manuale-operativo-design-docs/
- Designers Italia / Docs Italia, “Linee guida di design dei servizi web — Requisiti”
  (accessed 2026-10-14),
  https://docs.italia.it/italia/design/lg-design-servizi-web/it/versione-corrente/requisiti.html

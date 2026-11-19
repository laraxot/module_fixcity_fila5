---
title: "Legal content completion — frontoffice"
type: compliance-audit
status: blocked-by-tenant-input
updated: 2026-10-26
tags: [bmad, legal, gdpr, accessibility, frontoffice]
---

# Legal content completion — frontoffice

## Decision

The warning `Testo non ancora completo` must not be removed by inventing a
municipality, a DPO, an email address, a retention period, a legal basis or a
licence. `config/local/fixcity/ente.php` is intentionally empty and the legal
pages are tenant-owned content. Publication is therefore blocked until the
municipality supplies and approves the facts below.

The code already owns the safe publication gate:

- privacy: ten GDPR art. 13 sections in `AssessPrivacyPolicyAction`;
- legal notes: eleven service/legal-note sections;
- terms and media policy: tenant Markdown documents, currently without a
  structural assessment;
- accessibility: declaration data in `accessibilita.index.json`.

## Pages currently affected

| URL family | Required source | Gate | Current blocker |
|---|---|---|---|
| `/{lang}/privacy` | `lang/{lang}/policy.md` | GDPR art. 13 | controller and operational facts absent |
| `/{lang}/note-legali` | `lang/{lang}/legal-notes.md` | 11 sections | document absent |
| `/{lang}/terms` | `lang/{lang}/terms.md` | stub check only | framework stub / no entity approval |
| `/{lang}/media-policy` | `lang/{lang}/media-policy.md` | file existence only | document absent |
| `/{lang}/accessibilita` | `accessibilita.index.json` | accessibility assessment | status, dates and feedback contact absent |

## Required signed input from the municipality

1. Official controller name, registered address, tax/VAT identifiers, email
   and PEC.
2. DPO/RPD name and contact, or a written confirmation that no DPO is
   required with the legal reason.
3. Purpose and legal basis for every flow: account, ticket, geolocation,
   attachments, publication, notifications, logs and analytics.
4. Whether a ticket is an informal report, an administrative request, or can
   start a protocolled procedure; emergency and alternative channels.
5. Recipients, processors, sub-processors, hosting location and transfers
   outside the EEA, including maps, analytics, CAPTCHA, fonts and media.
6. Retention periods or approved criteria for accounts, tickets, attachments,
   consent records, moderation records and logs.
7. Moderation, publication, anonymisation and takedown rules for user content.
8. Licence for uploaded text/images and attribution requirements.
9. Accessibility declaration status, evaluation method/date, feedback email,
   publication/update dates and known non-conformities.
10. Cookie inventory and consent configuration confirmed against the deployed
    runtime, not only against configuration files.

## Official references consulted (2026-10-26)

- [GDPR 2016/679 — EUR-Lex](https://eur-lex.europa.eu/eli/reg/2016/679/oj?locale=it), especially articles 5–6, 12–14, 15–22, 25, 32 and 37.
- [Garante Privacy — cookie FAQ](https://www.garanteprivacy.it/faq/cookie).
- [Garante Privacy — cookie guidelines, 10 June 2021](https://www.garanteprivacy.it/home/docweb/-/docweb-display/docweb/9677876).
- [Law 4/2004 — Normattiva](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4).
- [CAD, Legislative Decree 82/2005 — Normattiva](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2005-03-07;82).
- [Legislative Decree 33/2013 — Normattiva](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2013-03-14;33).
- [Privacy Code, Legislative Decree 196/2003 — Normattiva](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2003-06-30;196).
- [AgID accessibility](https://www.agid.gov.it/it/design-servizi/accessibilita) and [accessibility declaration portal](https://form.agid.gov.it/).

These references guide the structure; they do not replace the municipality’s
legal review, data inventory, DPIA where required, or approval by the DPO and
legal office.

## Completion gate

The pages may be marked publishable only when the tenant documents pass the
existing assessment tests and a human review confirms that every fact matches
the deployed service. A green automated test alone is not legal approval.

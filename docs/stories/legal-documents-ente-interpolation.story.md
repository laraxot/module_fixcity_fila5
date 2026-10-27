---
title: "Complete legal documents with ente data from config"
type: story
tags: [fixcity, legal, gdpr, privacy, ente, design-comuni]
created: 2026-10-26
updated: 2026-10-26
qmd: "legal documents privacy notice ente tokens controller dpo"
issues: []
discussions: []
status: review
---

# Complete legal documents with ente data from config

- BMAD phase: Quick Flow (dev story)
- Owner: Modules/Fixcity (Sixteen `legal-document` block)

## Problem

The pages `/privacy`, `/note-legali`, `/media-policy` and `/terms` showed the "Testo non ancora completo" notice. `policy.md` was a short generic text, `terms.md` was the "Edit this file" template (it, en only), `legal-notes.md` and `media-policy.md` did not exist.

## Decision

- Texts in `config/local/fixcity/lang/{it,en,de,es}/{policy,legal-notes,media-policy,terms}.md` describe what the code really does (verified on disk, see "Facts").
- Controller and DPO identity is never written in the text. The text uses `{{ente.<field>}}` tokens. `InterpolateEnteTokensAction` replaces them from `GetEnteDataAction` (`config('ente')`) inside `GetPublishedLegalDocumentAction`, so the page, the section assessment and the report wizard all see the same text.
- An empty or unknown field becomes `[[ente.<field>]]`. `AssessPrivacyPolicyAction` flags it as a placeholder, so the notice lists only the missing ente data.
- Values are escaped as literal Markdown (ASCII punctuation backslash-escaped, lines joined). Emails, already validated by `GetEnteDataAction`, stay raw so they render as `mailto:` links.
- `media-policy.md` and `terms.md` use no ente tokens (they are not assessed, so a missing value would not be flagged).

## Ente fields the controller must still provide (`config/local/fixcity/ente.php`)

| Field | Used in |
|---|---|
| `name` | privacy: controller; legal notes: owner |
| `address` | privacy: controller; legal notes: owner |
| `pec` | privacy: controller; legal notes: owner, contacts |
| `dpo_name` | privacy: DPO |
| `dpo_email` | privacy: DPO (also the rights contact) |
| `tax_code` | legal notes: owner |
| `email` | legal notes: contacts |

## Facts used in the texts (code evidence)

- Account: users (name, email, hashed password, lang), profiles (phone, avatar, preferences). Registration stores consents with IP and user agent (`Modules/Gdpr/app/Actions/SaveGdprConsentsAction.php`).
- Google OAuth: name, email, Google id, avatar URL (`Modules/User/app/Actions/Socialite/*`). No SPID or CIE.
- Gravatar: MD5 of the email, loaded by the reader's browser (`Modules/Comment/app/Actions/ResolveGravatarUrlAction.php`).
- Tickets: title, description, type, address, lat/lng, status, code; up to 5 files of 10 MB (JPEG, PNG, PDF). Public after leaving `pending`, without owner name, email or photos (`BuildPublicTicketDetailAction.php`, `TicketStatusEnum.php`).
- Comments public with name and avatar, no prior moderation (`config/local/fixcity/comments.php`). Ratings 1-5 by the owner once. Followed tickets. Email notifications only to verified emails, can be turned off.
- Cookies: session, XSRF-TOKEN, remember, `__cookie_consent` 180 days; no analytics.
- Maps: OpenStreetMap tiles, Nominatim, staticmap.openstreetmap.de, Esri layers; browser geolocation.
- Activity log configured at 365 days. No automated decisions: priority `low` and status `pending` for all, assignment by staff.

## Follow-ups for the controller and the team

1. Fill `config/local/fixcity/ente.php` (table above).
2. Legal review of the texts by the ente's lawyer and DPO before production.
3. Retention: no scheduled `activitylog:clean` and no purge of deleted accounts was found. Schedule them so the stated 12 months and the account deletion statement are true.
4. EXIF/GPS is not stripped from uploaded photos. The texts warn users; stripping on upload is better.
5. Remove the preconnect to fonts.googleapis.com / fonts.gstatic.com and the dns-prefetch to google-analytics.com in `Modules/Cms/resources/views/components/metatags.blade.php`, then drop the Google Fonts line from the privacy notice.
6. Account deletion route `/{lang}/account/delete` exists but the personal area does not link it; the terms say "write to the municipality".
7. A Google Maps API key is hard-coded in `config/local/fixcity/google.php`: revoke it and move it to env.
8. Staff see tickets of all municipalities (DD-1, undecided); relevant for the recipients section in multi-ente deployments.
9. Gravatar: Automattic's DPF certification covers only WordPress VIP; the notice cites its standard contractual clauses instead.

## Sources consulted

- Regulation (EU) 2016/679, EUR-Lex: https://eur-lex.europa.eu/eli/reg/2016/679/oj
- D.Lgs. 196/2003 as amended by D.Lgs. 101/2018, art. 2-ter (Gazzetta Ufficiale consolidated text; amended by D.L. 139/2021, L. 205/2021)
- Garante, contacts: https://www.garanteprivacy.it/home/footer/contatti
- Garante, how to file a complaint: https://www.garanteprivacy.it/home/diritti/come-agire-per-tutelare-i-tuoi-dati-personali
- Garante, cookie guidelines 10 June 2021 (doc. web 9677876): https://www.garanteprivacy.it/home/docweb/-/docweb-display/docweb/9677876
- Commission Implementing Decision (EU) 2023/1795 (EU-US DPF): https://eur-lex.europa.eu/eli/dec_impl/2023/1795/oj/eng
- Google DPF statement: https://policies.google.com/privacy/frameworks
- Automattic privacy policy (covers Gravatar, contractual clauses): https://automattic.com/privacy/
- Automattic DPF listing (WordPress VIP only): https://www.dataprivacyframework.gov/participant/4709 and https://wpvip.com/privacy-framework/
- Esri DPF: https://trust.arcgis.com/en/compliance/dpf.htm
- Designers Italia, legal notes and social media policy model: https://designers.italia.it/note-legali/
- Designers Italia, Comuni model architecture (legal notes in the footer): https://designers.italia.it/modelli/comuni/
- CAD (D.Lgs. 82/2005) art. 52 open data by default; TUEL (D.Lgs. 267/2000); L. 150/2000; L. 241/1990; D.P.R. 445/2000 art. 68; D.Lgs. 104/2010.

## Verification

- `PEST_OUT=... bash bashscripts/tools/pest-scratch-db.sh --test-directory=Modules/Fixcity/tests Modules/Fixcity/tests/Unit/InterpolateEnteTokensActionTest.php Modules/Fixcity/tests/Feature/Pages/LegalDocumentsCompletenessTest.php` (plus the existing legal page and assessment tests).
- PHPStan 0 errors on the touched PHP files; Pint passed.

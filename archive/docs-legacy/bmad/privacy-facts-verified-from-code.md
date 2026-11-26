---
title: "Privacy facts verified from code (input for the art. 13 notice)"
type: compliance-audit
status: reference
updated: 2026-10-26
tags: [bmad, legal, gdpr, privacy, frontoffice, cookies, retention]
related:
  - ./legal-content-completion.md
  - ./completion-gap-register.md
---

# Privacy facts verified from code

How to use it: the privacy notice, the legal notes and the cookie policy may only state what is true. This page lists what the
code and the dev site actually do (GET-only checks, no database writes), each point marked VERIFIED or UNVERIFIED. Entity data
(controller, DPO, providers, retention periods) is not here: it comes from the municipality (see the legal content completion
page). Read the "Contradictions" section before publishing any text; several statements in the current texts are false.

Three points were re-checked by hand on 2026-10-26 after the audit: Gravatar images on `/it/tickets/554` (4), original photo URLs in
`/api/tickets/list` served without login by `/storage/...` (200), and no `remember_web` nor Gravatar in `/it/cookie-policy`.


Paths are relative to `laravel/` (repo root `/mnt/nas07/var/www/_bases/base_fixcity_fila5`). Dev site `http://192.168.1.40:8000` runs APP_ENV=local (`.env:2`).
"Prod" values come from `.env.production.example`, which is an example, not the real production env. Status: VERIFIED = read in code and/or seen with curl (GET only); UNVERIFIED = inferred, not exercised, or depends on infrastructure. No DB write, no seed, no test run.

## 1. Personal data collected

- 1.1 Registration form: first_name, last_name, email, password, password_confirmation (all required); privacy_accepted and terms_accepted (required checkboxes); marketing_consent (optional). Evidence: `Modules/Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php:64-70`; `Themes/Sixteen/resources/views/filament/widgets/auth/register.blade.php:18-22,28,35,42`; curl `/it/auth/register` shows the same 8 `wire:model` names. VERIFIED.
- 1.2 Account row also gets type=customer_user, lang=current locale, email_verified_at=now() with no verification step, password via Hash::make. The page says "Ti invieremo un'email di conferma" (`Modules/Gdpr/lang/it/register.php:60`) but nothing sends it. Evidence: `Modules/Gdpr/app/Actions/Validation/ValidateUserDataAction.php:35-40`. VERIFIED.
- 1.3 At registration the server also stores: 3 consent rows with IP, user agent, document_version (hard-coded '1.0'), locale (`Modules/Gdpr/app/Actions/SaveGdprConsentsAction.php:21-24,67-71`); an activity-log row with IP and user agent (`Modules/User/app/Actions/Activity/LogRegistrationAction.php:24-25`). VERIFIED.
- 1.4 Login form: email, password, "remember" checkbox (curl `/it/auth/login`). Each login stores IP, user agent, time in authentication_logs and a device row (platform, browser, device type): `Modules/User/app/Listeners/LoginListener.php:44,71-72`; `Modules/User/app/Actions/GetCurrentDeviceAction.php:61-72`. VERIFIED.
- 1.5 Profile model can hold phone, address, birth_date, gender (`Modules/User/app/Models/BaseProfile.php` fillable) but registration does not ask them and no FO form collecting them was found. Personal-area pages need login: UNVERIFIED.
- 1.6 Reporting needs login. Guest GET `/it/tickets/create` returns 302 to `/it/auth/login` (curl). `CreateTicketWizardWidget.php:89-93` stops without a user; `Modules/Fixcity/app/Actions/CreateTicketAction.php:24-30` throws without owner. No anonymous reporting. VERIFIED.
- 1.7 Wizard fields (`Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`): step 1 privacy checkbox required (:171-175); type required (:320-323); title required, 3-255 chars (:324-327); description required, min 10, FO max 200 (:328-331,352); address text required (:287); map point lat/long required and range-checked (:272-275,193-205); photos optional, max 5, jpeg/png/pdf, 10 MB each (:332-339). VERIFIED.
- 1.8 Contact data is not typed in the wizard: author card and summary read the logged-in account (name, email, profile phone if any): `BuildTicketReviewSummaryAction.php:145`; `TicketForm.php:372-382`. Ticket stores owner_id (`CreateTicketWizardWidget.php:115`). tickets table has a nullable `email` column but the wizard path never sets it. VERIFIED.
- 1.9 The wizard's privacy checkbox is only validated; no code stores that acceptance (rg `privacyAccepted` in `Modules/Fixcity/app`: form and validation only). Status and priority are forced server side to pending/low (`CreateTicketWizardWidget.php:118-119`). VERIFIED (absence).
- 1.10 Map picker asks the browser for geolocation when no point is set (`geolocateWhenEmpty()` `TicketForm.php:272`; `Modules/Fixcity/resources/js/components/coordinate-picker-lit.js:134`); coordinates reach the server only on submit. Code only, browser not run: UNVERIFIED in browser.
- 1.11 Comments: only on a ticket page, login required, anonymous off (`config/local/fixcity/comments.php:29`), auto-approved (:60), reactions off; writers = users allowed to `update` the ticket, i.e. the owner and PA staff (`Modules/Fixcity/app/Policies/TicketPolicy.php:35-40`; `Themes/Sixteen/resources/views/components/blocks/ticket-detail/design-comuni.blade.php:36-38,132-143`). VERIFIED.
- 1.12 Rating: owner of a resolved ticket gives 1-5, stored with user_id (`Modules/Fixcity/app/Actions/SubmitCitizenTicketRatingAction.php:80`). VERIFIED.
- 1.13 Accessibility feedback form: problem (required, 10-3000), page (optional), contact email (optional); emailed to the entity's mailbox, not stored; 5/hour/IP; hidden when no mailbox is configured (dev page `/it/accessibilita` shows no form). `Modules/Fixcity/app/Livewire/AccessibilityFeedbackForm.php:22,38-40,49-66`; `SendAccessibilityFeedbackAction.php:21`. VERIFIED.
- 1.14 FAQ "rate this page" widget (stars, options, note): Alpine state only, no request (`Themes/Sixteen/resources/views/components/pages/faq.blade.php:147-195`; gap register B-05/E-07). VERIFIED.
- 1.15 Search box: GET form to `/it/ricerca?q=` (curl). No storage of queries found (`Modules/Fixcity/app/Actions/Search/SearchFrontOfficeAction.php` has no Log/activity). The term sits in the URL, so web-server logs hold it: UNVERIFIED (infra).
- 1.16 Tracking by code: `/it/tickets/track?code=...` is a GET (code in URL); 10 wrong codes per 60 s per IP then 429 (`GetPublicTicketByCodeAction.php:26,29`). "Segui": ticket_id + user_id stored, POST throttled 30/min (`routes/web.php:37`). VERIFIED.
- 1.17 `marketing_consent` is stored but no code sends marketing or reads it (rg in Fixcity, Notify, Blog, Event, User app: nothing); DCP-200 DEC-08 proposes removing it. VERIFIED (absence).

## 2. Public vs private ticket data

- 2.1 Public statuses: open, in_review, in_progress, on_hold, resolved, closed, reopened (`Modules/Fixcity/app/Enums/TicketStatusEnum.php:46-57`). draft and pending are not public. A ticket needs a non-null location (`BuildPublicTicketsQueryAction.php:27,40-47`). curl `/api/tickets/geojson`: 484 features, only those 7 statuses. VERIFIED.
- 2.2 Public list `/it/tickets` (`ListPublicTicketsAction.php:54`): id, title, type, status, creation day, address, 300-char excerpt. No author, code, images, time. VERIFIED.
- 2.3 Public detail `/it/tickets/{id}` (`BuildPublicTicketDetailAction.php:26-29,82-100`): id, title, type, status, date, address, latitude, longitude, full description, timeline (day, from/to status, reason), is_following; tracking_code only for the owner. Never owner_id, email, name, images. Owner can open own ticket in any status (:115-117). VERIFIED.
- 2.4 `/api/tickets/geojson`: no auth, `Access-Control-Allow-Origin: *`, fields id, title, type, address, city, status, detail_url + coordinates (curl). VERIFIED.
- 2.5 Photos ARE exposed by public JSON: `/api/tickets/list` (`Modules/Fixcity/resources/views/pages/api/tickets/list.blade.php:38-49`) and `/api/ticket-details/{id}` (`BuildTicketPublicDetailsPayloadAction.php:45`) return `getFullUrl()` of the original files. Files load without login: curl `/storage/72/reference-mobile.png` gives 200 image/png. 8 of the first 50 tickets had images. Matches gap A-10. VERIFIED.
- 2.6 EXIF/GPS is not stripped: no EXIF code in Fixcity/Media app code; the only strip (`--strip-all`, `config/media-library.php:149`) runs inside Conversion (`vendor/spatie/laravel-medialibrary/src/Conversions/Conversion.php:43-46`) and Ticket registers no conversions (`Ticket.php:423-430`); jpegoptim is not installed on this host. Originals stay on disk `public` (`config/local/fixcity/media-library.php:34`). Code VERIFIED; EXIF content of any stored file not inspected: UNVERIFIED.
- 2.7 The tracking code is a capability token: anyone holding it sees title, description, image URLs, status, timeline even for non-public tickets (`Themes/Sixteen/resources/views/components/pages/ticket-track.blade.php:36-45`; `GetPublicTicketByCodeAction.php:15-17`). The code appears in the confirmation URL `?code=` (`CreateTicketWizardWidget.php:167-170`) and in the owner's notification (`TicketStatusChangedNotification.php:17-19`). VERIFIED.
- 2.8 Comments on a public ticket are readable by guests with the author's full name ("Commento di Nome Cognome") and a Gravatar image. curl `/it/tickets/554`: 4 gravatar `<img>`, 4 comment articles. If a reporter comments, their name becomes public. VERIFIED.
- 2.9 Public tickets are in `sitemap.xml` (curl: 1936 ticket URLs = 484 x 4 languages) and `robots.txt` does not disallow them, so search engines may index title, address, description. VERIFIED.

## 3. Cookies and browser storage

- 3.1 `curl -D` on `/it`, `/it/tickets`, `/it/blog`, each: `XSRF-TOKEN` (Max-Age 7200, path /, SameSite=lax, not HttpOnly), `fixcity_session` (Max-Age 7200, HttpOnly, SameSite=lax), `PHPDEBUGBAR_STACK_DATA` (session cookie, HttpOnly). The last is dev only: DEBUGBAR_ENABLED=true (`.env:5`), prod example false (`.env.production.example:5`). VERIFIED.
- 3.2 Session config: lifetime 60 min prod / 120 otherwise (`config/session.php:38`), expire_on_close true in prod (:41), Secure only in prod (:178), HttpOnly true (:191), SameSite lax (:208); driver redis in prod, file in dev (`.env:40`). VERIFIED.
- 3.3 Cookie `remember_web_<sha1>` is set when the user ticks "remember" (`LoginWidget.php:81`); Laravel duration 576000 min = 400 days (`vendor/laravel/framework/src/Illuminate/Auth/SessionGuard.php:62`). It is NOT in the cookie inventory (`Modules/Gdpr/app/Actions/Cookie/GetCookieInventoryAction.php:28-40` lists session, XSRF, consent only; live `/it/cookie-policy` lists the same 3). VERIFIED.
- 3.4 `__cookie_consent`: written by JS only after the user clicks a banner button, 180 days, SameSite=Lax, Secure on HTTPS (`Modules/Gdpr/resources/views/cookie-consent/index.blade.php:82,142`; `Modules/Gdpr/config/config.php:32`). Banner is info-only (live text: "solo cookie tecnici ... Non usiamo cookie di profilazione né di analisi"); `config/cookie-consent.php` says 365 but the banner uses 180. VERIFIED.
- 3.5 No locale cookie: locale lives in the session (`Modules/Cms/app/Providers/FolioVoltServiceProvider.php:119`; LocaleCookieRedirect commented out :121). VERIFIED.
- 3.6 Web storage on the served FO pages (`/it`, `/it/tickets`, `/it/blog`, `/it/tickets/554`, login): only a READ of localStorage `dark_mode` (inline script, `/it` HTML line 34). The writer (`Themes/Sixteen/public/assets/theme-behavior-DTzH7RaW.js:1`, dark toggle `#darkModeToggle`) has no element in those pages. No sessionStorage in the FO bundles. Other templates use localStorage (`cookie-bar.blade.php`: cookie_consent, cookie_preferences; `accessibility/font-size.blade.php`: font-size; contrast toggles; UI dark-mode-switcher) but are not in the served HTML: UNVERIFIED whether rendered on logged-in pages.
- 3.7 Staff panel: `Modules/UI/app/Filament/Widgets/DarkModeSwitcherWidget.php:31` queues cookie `dark_mode` (30 days). Staff only: UNVERIFIED.

## 4. Third-party requests from the visitor's browser

- 4.1 External hosts in the HTML of `/it`, `/it/tickets`, `/it/blog`, `/it/events`, `/it/tickets/track`, login, register, privacy, create: only www.w3.org and schema.org (namespaces/JSON-LD, not requests). Fonts are local; no analytics, CDN, social embed or iframe on those pages. Matomo is not wired (`Themes/Sixteen/config/sixteen.php:455-458` null; `.env` MATOMO_* are placeholders such as `your.matomo-install.com`). VERIFIED.
- 4.2 ON PAGE VIEW, OpenStreetMap tiles `https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`: `<map-lit>` is in `/it` and `/it/tickets`; `<coordinate-picker-lit readonly>` in `/it/tickets/{id}`; default layer "street" (`Themes/Sixteen/public/assets/theme-map-C2GBZ8X-.js:137`). Tile host gets IP, user agent, Referer (origin only, see 9.4). Allowed in CSP (`config/security.php:147`). Seen in HTML/JS, not in a real browser trace: UNVERIFIED network trace.
- 4.3 ON PAGE VIEW of tickets with comments, `https://www.gravatar.com/avatar/<md5 of commenter email>?d=mp` (curl `/it/tickets/554` lines 936, 970, 1004, 1038; `Modules/Comment/app/Actions/ResolveGravatarUrlAction.php:22-24`; `Modules/Comment/app/Models/Concerns/InteractsWithComments.php:41`). Discloses an md5 of the commenter's email in public HTML and the visitor's IP to Gravatar. Profile avatar uses a sha256 variant (`Modules/User/app/Models/BaseProfile.php:155`). Logged-in header also falls back to Gravatar (`Themes/Sixteen/resources/views/components/header-comune.blade.php:55`; whether this component is in the FO layout: UNVERIFIED). Not in the CSP allow-list. VERIFIED (code and curl).
- 4.4 ONLY ON USER ACTION: Nominatim `https://nominatim.openstreetmap.org/search` and `/reverse` (typed address, marker move; `theme-map-C2GBZ8X-.js:79`); layer switch to tile.openstreetmap.fr and `server.arcgisonline.com` (World_Imagery, World_Topo_Map) (:137), both outside the CSP allow-list; outbound links on click to `www.google.com/maps?q=`, `maps.openrouteservice.org/directions`, `www.openstreetmap.org/?mlat=`. VERIFIED (code).
- 4.5 Compiled theme CSS holds 3 background-image URLs on `flowbite.s3.amazonaws.com` (`Themes/Sixteen/public/assets/app-B3b5F1HC.css`; used by Blog blocks `leaderboard`, `article_list/v6`): not requested on the pages tested; other pages UNVERIFIED.
- 4.6 Source `Modules/Fixcity/resources/js/components/geopoint-picker-lit.js:67` and `map-picker-lit.js:72` inject a stylesheet from `unpkg.com`; not present in theme bundles. Whether any FO page loads them: UNVERIFIED.
- 4.7 Dev only, exclude from prod: debugbar JS, a "browser logger" posting to same-origin `/_boost/browser-logs` (`/it` HTML line 104). Pulse loads `fonts.bunny.net` (staff, see 5.12).

## 5. Server-side recipients and processors

- 5.1 Mail: code default `log` (`config/mail.php:17`); dev `sendmail` (`.env:49`); prod example `smtp`, host placeholder `SECURE_SMTP_HOST` (`.env.production.example:36-37`). Provider: NOT FOUND (go-live task, `Modules/Fixcity/docs/stories/dcp-208-...story.md:34`). Data sent: recipient email, tracking code, ticket title and status (`TicketStatusChangedNotification.php:51-54`), accessibility feedback text and optional email. Mail goes out only for verified emails with the preference on; registration marks every email verified (1.2). VERIFIED.
- 5.2 Queue/session/cache: dev `sync`/`file` (`.env:39-40`); prod example redis for queue, session, cache (`.env.production.example:25-28`), so notification payloads (ticket code) transit Redis. Real hosts: UNVERIFIED.
- 5.3 File storage: media disk `public`, local (`config/local/fixcity/media-library.php:34`, env MEDIA_DISK). S3 (`eu-west-1`, `.env.production.example:48`) is used only if MEDIA_DISK is set; not set in the example. Real prod: UNVERIFIED.
- 5.4 Google/social OAuth: code present (`Modules/User/routes/socialite.php`); button shown only when client id, secret and redirect are all set (`Themes/Sixteen/resources/views/components/utilities/google-button.blade.php:9`; `IsProviderConfiguredAction`). Dev: GOOGLE_CLIENT_ID set but no button in `/it/auth/login` or `/register` (curl). Off by default. If on, Google learns the login and the app stores name, email, avatar. VERIFIED (dev), prod UNVERIFIED.
- 5.5 SPID/CIE: controllers and `Themes/Sixteen/routes/auth.php:27-45` exist, but `php artisan route:list --path=auth/spid` finds nothing and curl `/auth/spid/*`, `/auth/cie/login` give 404. Not active. VERIFIED.
- 5.6 Firebase push, Telegram, SMS: config and `Modules/Notify` classes exist, but no Fixcity/Gdpr/Comment/User code uses them; FO notifications use database + mail only (`TicketStatusChangedNotification.php:51-54`); FIREBASE_CREDENTIALS and TELEGRAM_BOT_TOKEN empty in `.env`. VERIFIED (dev).
- 5.7 AI/OpenAI: Module AI is enabled (`config/local/fixcity/modules_statuses.json:13`); `config/openai.php:11` reads OPENAI_API_KEY (empty in `.env`). `Modules/AI/app/Actions/ClassifyTicketAction.php:25` would send ticket title and description to the model (cached by md5), but nothing outside Modules/AI references it, and AI route files hold no routes. No citizen data goes to an AI provider today; provider undecided (`Modules/Fixcity/docs/bmad/completion-gap-register.md:81`, F-08/F-10). VERIFIED.
- 5.8 `Modules/Fixcity/app/Actions/GeocodeTicketAddressAction.php:54` would send ticket lat/lon to Nominatim server side, but has no caller (rg). VERIFIED.
- 5.9 Pwned Passwords: password rule `uncompromised()` is on (`Modules/User/app/Datas/PasswordData.php:35,74`; `Modules/User/app/Providers/UserServiceProvider.php:157`), so each new password triggers a request to `https://api.pwnedpasswords.com/range/<first 5 chars of SHA-1>` (`vendor/laravel/framework/src/Illuminate/Validation/NotPwnedVerifier.php:89`). Server to third party; no email, no full hash. VERIFIED.
- 5.10 Error tracking: Sentry DSN key is empty in the prod example (:100), package not installed (no `vendor/sentry`, not in composer.json). Slack log channel exists (`config/logging.php:76-84`) and `.env` has a webhook, but the default stack is `single` (6.3), so it is not used unless LOG_STACK says so. VERIFIED.
- 5.11 Pulse: installed, on by default (`config/pulse.php:46`), recorders on (requests, exceptions, queues, slow jobs/queries, user requests), kept 7 days (:86). Gate `viewPulse` = super-admin (`UserServiceProvider.php:152`), yet dev `/pulse` answered 200 to anonymous curl (local env bypass). What exactly it stores per user: UNVERIFIED.
- 5.12 Activity log (spatie) on (`config/local/fixcity/activitylog.php:10`): registration (IP, UA), ticket activities with actor id. Staff only. VERIFIED.
- 5.13 Hosting, web server, CDN, backups: NOT FOUND in code.

## 6. Retention and rights

- 6.1 No scheduler: `routes/console.php:10` has only `inspire`; no `Schedule::` elsewhere (rg app, bootstrap, routes, Modules/*/app/Providers, Modules/*/routes); gap register A-07. Nothing is pruned automatically (tickets, consents, auth logs, devices, notifications, activity log). VERIFIED.
- 6.2 Retention settings that no code enforces: `Modules/Gdpr/config/config.php:45-48` (5 years personal, 1 year logs; insurance boilerplate) is read nowhere; `config/local/fixcity/activitylog.php:16` (365 days) needs `activitylog:clean`, never scheduled; `config/security.php` `logging.retention_days` 90 is not read. VERIFIED.
- 6.3 Logs: default channel stack -> `single` (`config/logging.php:57`), one file, no rotation in code; `daily` with 14 days (:72) only if LOG_STACK=daily (not set). Level error in dev (`.env:13`), warning in prod example (:10); IP/UA debug lines (`RegisterWidget.php:172`, `SaveGdprConsentsAction.php:78`) are below that. Session lifetime: see 3.2. VERIFIED.
- 6.4 Soft deletes: tickets table has deleted_at (`Modules/Fixcity/database/migrations/2026_06_10_140000_create_tickets_table.php`, `hasSoftDeletes: true`); `Modules/Fixcity/app/Models/BaseModel.php:15` uses SoftDeletes. Ticket delete is admin-only (`TicketPolicy.php:42-47`). `BaseUser.php:150` has `// use SoftDeletes;`. VERIFIED.
- 6.5 Account deletion: POST `/{lang}/account/delete`, auth (`Modules/Gdpr/routes/web.php:11`) runs `DeleteAccountAction`: revokes consents, writes an `account_deletion` consent row with IP, UA, reason, sets users.deleted_at/deleted_by, marks the user's activity rows deleted_at, logs out (`Modules/Gdpr/app/Actions/Account/DeleteAccountAction.php:24-27,41,50,67-90`). It does not erase or anonymise name, email, tickets, comments, auth logs. GET only redirects home (:43-45); no FO page or link serves the form (the `account/delete.blade.php` view is not returned by the action; `DeleteAccountWidget` is referenced nowhere); curl `/it/account/delete` gives 302 to login. No `deleted_at` check in `LoginWidget.php`, so a "deleted" user may still log in: UNVERIFIED (not executed). Story DCP-204 is `ready-for-dev` with DEC-10 open. VERIFIED (code).
- 6.6 Data export: `config/personal-data-export.php:9,19` exists, disk `personal-data-exports` is not defined in `config/filesystems.php`; no ExportsPersonalData class, route or UI. NOT FOUND.
- 6.7 Data-subject request register: only a draft (`Modules/Gdpr/docs/blueprints/registro-richieste-interessato.md`); no `data_subject_requests` model or migration. NOT FOUND.
- 6.8 Consent records: Gdpr Treatment/Consent models keep user, treatment, accepted_at, revoked_at, IP, UA, metadata (`SaveGdprConsentsAction.php:58-72`). The version is a constant '1.0', not tied to the text shown; the wizard acceptance is not recorded (1.9). VERIFIED.
- 6.9 Controller and DPO contacts: `config/local/fixcity/ente.php:24-40` (name, address, email, pec, dpo_name, dpo_email) all empty; live `/it/privacy` shows "Testo non ancora completo" with 10 empty sections; `/it/contatti` returns 404. VERIFIED.

## 7. Automated decisions and profiling

- 7.1 None found. Server sets every new ticket to pending/low (`CreateTicketWizardWidget.php:118-119`); changes come from staff Filament actions (`ChangePriority`; `AssignTicketAction` only called from `ViewTicket.php`); no spam score, captcha or honeypot in FO code (rg); AI classification exists but is not wired (5.7); SLA code is an aggregate staff metric (`GetTicketSlaMetricsAction.php`), not per citizen. Throttles are technical, per IP. VERIFIED by search (absence).

## 8. Minors

- 8.1 No age field, declaration or minimum age at registration (`RegisterWidget.php:64-70`; register page text, curl). Profile has birth_date but the form does not ask. DCP-165 is `needs-decision` (`Modules/Gdpr/docs/stories/dcp-165-eta-minima-e-minori-nella-registrazione.story.md:6`); DCP-178 row 12 "non gestita". VERIFIED.

## 9. Security measures that can be stated

- 9.1 Passwords hashed with Hash::make (`ValidateUserDataAction.php:35`), driver bcrypt by default (HASH_DRIVER not set in `.env`). VERIFIED.
- 9.2 Password rule: min 8, mixed case, letters, numbers, symbols, not in known breaches (`PasswordData.php:30-36,59-76`; tenant override `config/local/fixcity/password.php` returns `[]`). Page text matches ("Minimo 8 caratteri ..."). VERIFIED.
- 9.3 Rate limits: login 5 per 60 s per email+IP (`LoginWidget.php:33,36,74-75`); registration 10 per 600 s per IP (`RegisterWidget.php:32,34`); wrong tracking codes 10 per 60 s per IP; accessibility form 5 per hour per IP; follow 30/min; comment submit (`Modules/Comment/app/Actions/Comment/EnsureCommentRateLimitAction.php:34`). Public `/api/tickets/*` have no limit seen in code (DCP-200 DEC-24 proposes 60/min): not load-tested, UNVERIFIED.
- 9.4 Headers (curl `-I /it`): X-Content-Type-Options nosniff, X-Frame-Options SAMEORIGIN, Referrer-Policy strict-origin-when-cross-origin, Permissions-Policy (camera, microphone, payment, usb, sensors denied; geolocation self), CSP `frame-ancestors 'self'` enforced; the full CSP is Report-Only (`app/Http/Middleware/SecurityHeaders.php:36-46`; `config/security.php:131`; prod example `SECURITY_CSP_MODE=report-only`, :81) and allows `'unsafe-inline'` and `'unsafe-eval'` scripts. HSTS only on HTTPS requests (:44; none on dev http). VERIFIED.
- 9.5 Session cookie HttpOnly, SameSite=lax, Secure in prod (3.2); CSRF cookie XSRF-TOKEN present. `URL::forceScheme('https')` depends on `XotData::forceSSL()` (`Modules/Xot/app/Providers/XotServiceProvider.php:312`); the prod example's FORCE_HTTPS, SECURE_COOKIES, SECURE_HEADERS keys are not read by any config. HTTPS termination: UNVERIFIED (infra).
- 9.6 Uploads: only image/jpeg, image/png, application/pdf, max 5 files, 10 MB (`TicketForm.php:332-339`; `Ticket.php:426`; `config/local/fixcity/media-library.php:40`). No malware scan: no ClamAV code, `scan_for_malware` in `config/security.php` is not read. Server-side sanitising of the HTML policy text: `html_input => strip` (`TicketForm.php:233`). VERIFIED.
- 9.7 Photos keep EXIF and are public through the JSON APIs (2.5, 2.6). Stating "metadata is removed" would be false. VERIFIED.
- 9.8 Inert config, do NOT cite as measures: in `config/security.php` only `headers.*` and `trusted_hosts` are read by code; `session`, `rate_limit`, `password` (min 12, expiration 90 days), `file_upload` (5 MB, malware scan), `two_factor`, `encryption`, `database.encrypt_at_rest`, `logging.retention_days` are never read (rg `config('security.`). There is no encryption of personal data at rest in code, only password hashing. VERIFIED.
- 9.9 Authorization: `TicketPolicy.php:18-40` (view/update), public queries filtered by status (2.1). Dev-only items to switch off in prod: debugbar, local-env Pulse access, boost logger.

## Contradictions with existing text

`config/local/fixcity/lang/it/policy.md` (13 lines, served as the wizard consent text and `/it/privacy` body):
- P1 "I dati sono accessibili esclusivamente agli uffici competenti": FALSE. Public: title, description, address, coordinates, type, status, dates, timeline (2.2-2.4); original photo URLs (2.5); commenter names and email hashes (2.8, 4.3); holder of the tracking code (2.7); indexed by search engines (2.9); processors and third parties (section 4, 5).
- P2 "conservati per il tempo necessario ... e agli obblighi di legge": not backed by code. Nothing is deleted automatically, tickets are soft-deleted, "account deletion" keeps the data (6.1-6.5).
- P3 "contattare l'ufficio responsabile tramite i recapiti pubblicati sul portale": no recapiti exist in config (6.9), `/it/contatti` is 404, no in-product way to exercise rights (6.5-6.7).
- P4 "Proseguendo, dichiari ... e acconsenti": the wizard uses a required checkbox, not "proseguendo", and the acceptance is not stored (1.9); consent as basis for a public-interest service is a legal point (DCP-165 notes art. 6(1)(e)).
- P5 "Sono trattati i dati di contatto, la posizione, la descrizione e gli eventuali allegati": incomplete. Missing: account data, IP and user agent, login/device logs, consent records, comments, ratings, follow data, third-party requests (4.x, 5.9). Contact data comes from the account, there is no contact field in the wizard (1.8).

`Modules/Fixcity/resources/templates/privacy-policy.it.md` (placeholders only; instructions that the facts constrain):
- T1 "Trasferimenti: ... altrimenti dichiarare che non ci sono trasferimenti": declaring "none" is unsafe. Browsers call OpenStreetMap (4.2, 4.4) and Gravatar (4.3); the server calls api.pwnedpasswords.com (5.9). Provider countries are outside the code: UNVERIFIED.
- T2 "Processi decisionali automatizzati ... altrimenti dichiarare che non esiste": a "none" statement is supported by the code (7.1).
- T3 "Diritti ... portabilità, revoca": no export tool, no request register, revocation only via the non-discoverable delete route (6.5-6.7). "Periodo di conservazione ... consensi e registri": no automatic limit exists (6.1-6.3).
- T4 "Destinatari ... fornitori del servizio": mail provider, hosting, Redis/S3 hosts are unknown to the code (5.1-5.3, 5.13).

`Modules/Fixcity/resources/templates/legal-notes.it.md`:
- L1 "Una segnalazione può essere resa pubblica (titolo, descrizione, indirizzo, stato e foto) dopo la presa in carico": incomplete and partly wrong. Public pages do not show photos, but the JSON APIs do (2.5); coordinates, type, date, timeline are also public (2.3).
- L2 "prima di quel momento è visibile solo a te e agli operatori": FALSE for anyone who has the tracking code (`/it/tickets/track?code=`, 2.7); the code travels in a URL and an email.
- L3 "per mostrarli il tuo browser contatta direttamente quei servizi (OpenStreetMap), che ricevono il tuo indirizzo IP": true for tiles (home, `/it/tickets`, ticket pages) and Nominatim (on action). Incomplete: Gravatar on pages with comments (4.3), ArcGIS and openstreetmap.fr on layer switch (4.4).
- L4 "L'ente può sospendere o chiudere un account": no FO suspend/close flow found in the code read (a `is_active` attribute exists on users); UNVERIFIED.
- L5 Links `privacy`, `cookie-policy`, `accessibilita` all answer 200 and `/it/terms` too (curl): consistent.

Related live texts: the register page promises a confirmation email that is never sent (1.2); the cookie policy page omits `remember_web_*` (3.3) and says only OSM is contacted (misses Gravatar, 4.3).

## Fixed after the audit

- 2026-10-26: the cookie policy lists the remember-me cookie (3.3). `GetCookieInventoryAction` adds the cookie of the guard (name from
  `getRecallerName()`, 400 days, technical) and the text `purpose_remember` exists in it, en, de, es. Test: `CookieInventoryTest`
  (red first, then green); cookie suites 36 passed; PHPStan 0; live `/it|en|de|es/cookie-policy` answer 200 with the row.
  Two sessions wrote the same change at the same minute and left a duplicate `use` (fatal on every page with the banner for about
  a minute); the version with the named constant and the WP29 reasoning was kept.

## Not fixed: decisions for the owner (do not publish a text that hides them)

- Gravatar on ticket pages with comments (4.3): remove it, proxy it, or declare it in the notice and in the cookie policy.
- Original photos with EXIF/GPS public through `/api/tickets/list` and `/storage/...` (2.5, 2.6): strip EXIF, serve a resized copy, or stop listing them.
- Tracking code in a URL and in an email gives access to non-public tickets (2.7): the legal notes say the opposite (L2).
- Retention, data export and a request register do not exist (6.1 to 6.7): the notice cannot promise periods or portability yet.
- The register page promises a confirmation email that is never sent (1.2); the wizard acceptance is not stored (1.9).
- `policy.md` (served as the wizard consent text) is false at P1 and must not be reused.

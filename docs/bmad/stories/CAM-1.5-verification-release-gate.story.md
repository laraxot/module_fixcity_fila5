---
story_id: CAM-1.5
epic: CAM-1
title: "Gate di verifica e rilascio mobile camera upload"
type: story
status: ready-for-dev
owner: Fixcity
priority: P1
---

# CAM-1.5: Gate di verifica e rilascio mobile camera upload

## Story

Come release owner, voglio prove ripetibili del roundtrip camera su browser e
runtime target, così il fix non viene dichiarato concluso sulla sola build.

## Acceptance Criteria (LOCKED)

1. Test applicativi da `laravel/` coprono il wizard, upload, cancel, replace,
   MIME contraffatto, oversize e cleanup.
2. Build e lint Sixteen passano dal contesto corretto e `graphify update .` è
   eseguito dopo le modifiche al codice.
3. La matrice 375/768/1440 px non mostra overflow e conserva accessibilità.
4. iPhone Safari HTTPS, Android Chrome HTTPS, Samsung Internet e desktop sono
   provati oppure marcati esplicitamente non verificabili.
5. Il dossier BMAD registra risultati, limiti infrastrutturali e nessuna falsa
   dichiarazione di completamento.

## Tasks / Subtasks

- [ ] Eseguire test Fixcity/Media dal root `laravel/`. (AC: #1)
- [ ] Eseguire lint/build Sixteen e graphify dal contesto corretto. (AC: #2)
- [ ] Eseguire smoke responsive/accessibility. (AC: #3)
- [ ] Eseguire la matrice device/HTTPS e raccogliere evidenze. (AC: #4)
- [ ] Aggiornare Dev Agent Record e decision log della story owner. (AC: #5)

## Dev Notes (LOCKED)

- I test Laravel si eseguono da `laravel/`; gli asset Sixteen dal tema, mai
  `php artisan` dentro il tema. [Source: ../../../../../../AGENTS.md]
- La verifica reale deve distinguere test locale da limiti di produzione e
  proxy. [Source: ./mobile-camera-upload.story.md]
- Le story operative sono la fonte di coordinamento; `docs/chat/` è storico.
  [Source: ../decision-log.md]

## Testing

- **Approach:** Pest/Feature + lint/build + browser E2E/manual device.
- **Key scenarios:** happy path JPEG/HEIC, error paths, retry, cancel,
  replacement, session loss, translated errors.
- **Per-AC verification:** AC #1 command reports; AC #2 asset/graph reports;
  AC #3 viewport screenshots/axe; AC #4 device matrix; AC #5 story audit.
- **Fixtures / mocks:** isolated scratch DB, deterministic binary fixtures,
  mobile browser profiles; nessun test distruttivo.
- **Edge cases:** proxy 413, FPM limits, offline interruption, permission
  denial, missing Imagick/libheif.

## Dependency Maps

- **Blocked by:** CAM-1.1, CAM-1.2, CAM-1.3, CAM-1.4.
- **Blocks:** release of the mobile camera upload fix.
- **External:** HTTPS devices, deployment runtime, browser automation.

## Owned File/Module Scope

- `laravel/Modules/Fixcity/docs/bmad/stories/mobile-camera-upload.story.md`
- `laravel/Modules/Fixcity/docs/bmad/decision-log.md`
- `laravel/Modules/Media/docs/bmad/decision-log.md`
- `laravel/Themes/Sixteen/docs/bmad/README.md`
- `laravel/Modules/Fixcity/docs/bmad/evidence/`

## Learnings from Previous Stories

- Build verde e test locali non provano i limiti del proxy o i dispositivi
  reali. (from CAM-1.1, CAM-1.2)
- Imagick va dichiarato non verificato se il delegate HEIC non è disponibile.
  (from CAM-1.3)

## Dev Agent Record

_(empty — to be completed by the external dev tool)_

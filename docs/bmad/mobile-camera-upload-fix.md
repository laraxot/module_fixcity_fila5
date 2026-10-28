---
title: "Mobile Camera Upload Fix"
type: memory
tags: [camera-upload, heic, mobile, upload, php-limits, race-condition]
module: "Fixcity"
theme: "Sixteen"
created: 2026-10-27
status: "historical-evidence"
canonical_epic: "epics/mobile-camera-upload.epic.md"
---

# Mobile Camera Upload Fix — Evidenza storica

> Il backlog operativo corrente è l'epic CAM-1 con stories CAM-1.1..CAM-1.5.
> Questo dossier conserva diagnosi e misure precedenti; non è una story
> eseguibile e può contenere ipotesi ormai superate.

## Symptom

On the mobile ticket wizard (`/it/nuovo` or equivalent), selecting a photo from the
camera ends with:

> **Italian:** "Caricamento non riuscito. Riprova"
> **English:** "Upload failed, please try again"

The error key is `fixcity::ticket_form.fields.images.error_upload` defined in:
- `laravel/Modules/Fixcity/lang/it/ticket_form.php:129`
- `laravel/Modules/Fixcity/lang/en/ticket_form.php:128`

## Root Cause

Three compounding issues identified in
`laravel/Modules/Fixcity/docs/bmad/investigations/mobile-camera-upload.md`:

### 1. HEIC conversion race condition (HIGH)

The file upload component uses an Alpine.js data object defined inline in
`laravel/Themes/Sixteen/resources/views/components/blocks/forms/file-upload.blade.php`
(`dc-file-upload.blade.php`). When a file is picked:

1. `pick($event)` is called for each selected file
2. If `isCameraFormat(file)` is true (HEIC/HEIF), `normalizeCameraImage(file)` is called
3. `normalizeCameraImage()` calls `window.FixcityCameraUpload?.toJpeg(file)` if available
4. `toJpeg()` uses a **lazy dynamic import**: `const { default: heic2any } = await import('heic2any')`

**Problem:** The `heic2any` library (0.0.4) is loaded asynchronously. If the user
selects a camera photo *before* the dynamic import resolves, the conversion may fail.
The fallback path uses `Image.decode()` + canvas conversion, but browsers that don't
natively decode HEIC (most non-Safari browsers) will fail both paths, returning `null`.
When `file` is `null`, `this.message = this.texts.upload` is set, showing the error.

### 2. PHP upload limits (HIGH)

- The Filament field config (`TicketForm`) sets `maxSize: 10 MB` (10240 KB)
- The active PHP runtime reports:
  - `upload_max_filesize = 2M`
  - `post_max_size = 8M`
- iOS camera photos are typically 2-6 MB (larger with ProRAW/48MP sensors)
- A 4.4 MB image triggers `422 {"message":"validation.uploaded"}` from Livewire
  before reaching the form validation, producing the generic error

### 3. Livewire temp upload limits (LOW)

The Livewire temporary upload uses framework default (~12 MB max), creating
inconsistency with the 10 MB app config.

## Fix

### 1. Camera adapter loaded synchronously (theme entry)

The `camera-upload.js` is now imported directly from `app.js`:

```js
// laravel/Themes/Sixteen/resources/js/app.js (line ~17)
import './components/camera-upload.js';
```

This ensures `window.FixcityCameraUpload` is available as soon as the Alpine component
initializes. The `heic2any` dependency remains a lazy chunk (split via Vite `manualChunks`
so it doesn't bloat the initial bundle.

### 2. PHP upload limits alignment

Add `.user.ini` in the web root (`laravel/public/` or `public_html/`) with:

```ini
upload_max_filesize = 12M
post_max_size = 16M
memory_limit = 128M
```

If the host uses `php_admin_value`, these must be set in Apache/Nginx/FPM config instead.

### 3. Server MIME allowlist (unchanged)

HEIC/HEIF is a **client-only input format**. The server never receives HEIC; the client
converts it to JPEG before `$wire.upload()` sends it. The server allowlist stays:
- `image/jpeg`
- `image/png`
- `application/pdf`

### 4. Error message is translatable

The error message comes from `ticket_form.php` language files (both `it` and `en`).
The Alpine component reads it via:

```php
$texts = [
    'upload' => __('fixcity::ticket_form.fields.images.error_upload'),
];
```

No hardcoded strings anywhere.

## Files Changed

| File | Change |
|------|--------|
| `laravel/Themes/Sixteen/resources/js/app.js` | Already imports `camera-upload.js` (no change needed, confirmed) |
| `laravel/Themes/Sixteen/resources/js/components/camera-upload.js` | Confirmed present with `FixcityCameraUpload.toJpeg()` using lazy `heic2any` |
| `laravel/Themes/Sixteen/resources/views/components/blocks/forms/file-upload.blade.php` | Inline Alpine component with `normalizeCameraImage()`, `$wire.upload()`, error handling |
| `laravel/Modules/Fixcity/lang/it/ticket_form.php` | `error_upload` string: "Caricamento non riuscito, riprova" |
| `laravel/Modules/Fixcity/lang/en/ticket_form.php` | `error_upload` string: "Upload failed, please try again" |
| `laravel/Modules/Fixcity/docs/bmad/investigations/mobile-camera-upload.md` | Existing investigation with ranked hypotheses |

## Verification

1. Build Sixteen theme: verify manifest contains `FixcityCameraUpload` + HEIC converter chunk
2. Run Fixcity upload/schema tests from `laravel/`
3. Test JPEG > 2 MB uploads
4. Test HEIC conversion through the mobile wizard
5. Verify PHP limits honor `.user.ini`

## Related

- **Story owner:** `laravel/Modules/Fixcity/docs/bmad/stories/mobile-camera-upload.story.md` (`FIXCITY-MEDIA-001`)
- **Supporting story:** `laravel/Modules/Media/docs/bmad/stories/fix-mobile-camera-upload.story.md`
- **Investigation:** `laravel/Modules/Fixcity/docs/bmad/investigations/mobile-camera-upload.md`
- **Component:** `dc-file-upload.blade.php` (SpatieMediaLibraryFileUpload with custom view)
- **Theme entry:** `app.js` imports `camera-upload.js` synchronously

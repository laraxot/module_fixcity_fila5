# Investigation: mobile camera attachment upload

## Symptom

On the mobile ticket wizard, selecting a photo from the camera can end with
`Caricamento non riuscito. Riprova` / `The image failed to upload`. Expected:
the photo reaches `data.images` and remains available in the wizard summary.

## Evidence

| Grade | Finding |
| --- | --- |
| A | The custom Design Comuni field calls `window.FixcityCameraUpload.toJpeg()` for HEIC/HEIF before `$wire.upload()`. |
| A | `camera-upload.js` was loaded only through an asynchronous, non-awaited preload. A user can select the camera file before that chunk finishes, so the HEIC conversion falls back to browser decoding; browsers that do not decode HEIC then reject the file locally. |
| A | The application field accepts 10 MB (`TicketForm`) while the active PHP runtime reported `upload_max_filesize=2M` and `post_max_size=8M`. A 4.4 MB image reproduced `422 {"message":"validation.uploaded"}` from Livewire, before the file reached the form, producing the generic upload failure. |
| B | The Livewire temporary upload rules use the framework default (12 MB), so the application and PHP limits were inconsistent even without HEIC. |

## Ranked hypotheses

1. **High — PHP request limits reject camera files.** The configured form limit
   is larger than the active runtime limit. The fix is the web-root `.user.ini`
   with 12 MB per file and 16 MB request headroom; infrastructure must allow
   user-level PHP overrides.
2. **High — HEIC converter race.** The converter was exposed only after an
   asynchronous preload. The fix is to import the small camera adapter from
   the theme entry point; its heavy `heic2any` dependency remains lazy.
3. **Low — storage or Media Library failure.** The failure occurs during the
   Livewire temporary upload, before the `attachments` collection is persisted;
   no evidence currently points to permanent storage.

## Implemented response

- Load the camera adapter synchronously with the Sixteen entry module so HEIC
  normalization is available before any file input interaction.
- Align the active web-root PHP upload limits with the existing 10 MB Filament
  field contract, including multipart headroom.
- Keep the server MIME allowlist unchanged: JPEG, PNG and PDF. HEIC/HEIF is a
  client-only input format converted to JPEG.
- Make the isolated browser fixture pass the same 12 MB / 16 MB PHP limits as
  the deployed web root; otherwise its PHP built-in server gives a false
  regression for any realistic camera photo.

## Verification contract

- Build the Sixteen theme and verify the manifest/bundle contains
  `FixcityCameraUpload` and a separate HEIC converter chunk.
- Run the narrow Fixcity upload/schema tests from `laravel/`.
- Exercise a JPEG above 2 MB and a HEIC camera image through the mobile wizard
  on a PHP web server that honours `.user.ini`; verify Nginx also allows at
  least 16 MB for the request.

## Environment limitation

The repository cannot change PHP limits for an already-running PHP built-in
server. Restart the web process after deploying `.user.ini`; if the host uses
`php_admin_value`, Apache/Nginx/FPM configuration must be changed there instead.

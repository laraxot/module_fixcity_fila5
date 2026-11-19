---
story_id: CAM-1.4
epic: CAM-1
title: "Messaggi localizzati e contratto accessibile dell’upload"
type: story
status: ready-for-dev
owner: Fixcity
priority: P1
---

# CAM-1.4: Messaggi localizzati e contratto accessibile dell’upload

## Story

Come cittadino multilingue, voglio capire stato, errore, retry e fallback del
caricamento in una lingua supportata e con tecnologie assistive.

## Acceptance Criteria (LOCKED)

1. Tutte le nuove chiavi camera/upload esistono in it/en/de/es e non ci sono
   stringhe UI hardcoded in Blade o JavaScript.
2. Sono localizzati almeno tipo non supportato, dimensione, preparazione,
   upload, retry, permesso negato e camera non disponibile.
3. Il campo espone label, istruzioni, errore e stato con associazioni ARIA e
   live region senza dipendere dalla fotocamera.
4. Picker galleria, tastiera, cancel e replace restano disponibili.

## Tasks / Subtasks

- [ ] Inventariare le chiavi usate dal campo e completare i quattro cataloghi.
  (AC: #1, #2)
- [ ] Aggiungere test che falliscano per chiavi mancanti o valori hardcoded.
  (AC: #1)
- [ ] Verificare `aria-describedby`, `aria-invalid`, stato live e focus.
  (AC: #3)
- [ ] Verificare percorso picker/tastiera senza camera. (AC: #4)

## Dev Notes (LOCKED)

- Il progetto è multilingua: i testi arrivano da `ticket_form`, mai da
  fallback in JS. [Source: ./mobile-camera-upload.story.md]
- Il campo possiede già testi per upload, preparazione, rimozione e formato;
  estendere il contratto esistente. [Source: ../../../resources/views/filament/forms/components/dc-file-upload.blade.php]
- Il tema Sixteen contribuisce al markup, ma Fixcity possiede le traduzioni e
  il dominio del wizard. [Source: ../../../../../Themes/Sixteen/docs/bmad/README.md]

## Testing

- **Approach:** unit test cataloghi + browser accessibility smoke.
- **Key scenarios:** quattro lingue, errore tipo, errore dimensione, retry,
  permission denied, keyboard-only.
- **Per-AC verification:** AC #1 translation test; AC #2 key/value matrix;
  AC #3 axe/manual ARIA; AC #4 keyboard/browser flow.
- **Fixtures / mocks:** locale matrix e Livewire upload failure stub.
- **Edge cases:** lingua senza chiave, messaggio lungo a 375 px, screen reader.

## Dependency Maps

- **Blocked by:** none.
- **Blocks:** CAM-1.5.
- **External:** Laravel translation loader, browser accessibility APIs.

## Owned File/Module Scope

- `laravel/Modules/Fixcity/lang/it/ticket_form.php`
- `laravel/Modules/Fixcity/lang/en/ticket_form.php`
- `laravel/Modules/Fixcity/lang/de/ticket_form.php`
- `laravel/Modules/Fixcity/lang/es/ticket_form.php`
- `laravel/Modules/Fixcity/tests/Unit/TicketFormImagesLangTest.php`

## Learnings from Previous Stories

- Non usare il messaggio generico Livewire come testo applicativo; il mapping
  deve essere esplicito e tradotto. (from CAM-1.1)

## Dev Agent Record

### 2026-10-27, Claude (sessione a9bceaa6): messaggi di validazione dell'upload (modulo Xot)

Quando l'upload falliva il cittadino leggeva chiavi grezze o inglese. Corretti i cataloghi globali `validation` in `Modules/Xot/lang`
(repo separato dal Fixcity):

| Chiave | Prima | Dopo |
|---|---|---|
| `uploaded` | it: chiave grezza | frase intera senza `:attribute` (Livewire passa il nome interno `data.images.f...`), in it, de, es |
| `file` | it, de: inglese | tradotta |
| `distinct` | it, de: inglese | tradotta |
| `mimetypes` | it: chiave grezza, visibile al rifiuto del file contraffatto | tradotta in it, de, es |

Test: `Modules/Xot/tests/Unit/ValidationMessagesTest.php`, caso «translates the upload rules in the language of the page» (it, de, es
per 4 chiavi). Rosso con `it mimetypes` non tradotta, poi file intero verde: 111 passati. Browser: il rifiuto del file contraffatto
mostra la frase italiana e nel testo della pagina non c'e' nessun `validation.*`.

**Non fatto**

- Il rifiuto in de, es, en non e' stato provato in browser (coperto solo dal test sulle chiavi).
- ARIA, live region e tastiera (AC3, AC4) non verificati qui.
- Il testo del campo «massimo 1.8 MB» e' vero per i PDF e fuorviante per le foto, che il browser riduce da sole: serve una frase
  distinta in it/en/de/es oppure la decisione di tenerla.
- `uploaded` in de e es sta in `Modules/User/lang`, in en in `laravel/lang/en/validation.php`: scritti dall'altra sessione.

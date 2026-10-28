{{--
    Allegati del passo dati: markup di segnalazione-02-dati
    (`.btn-wrapper > label + .upload-wrapper (miniatura, nome, elimina) + hr + button.btn-primary "Carica file" + p aiuto`).
    Uso: `SpatieMediaLibraryFileUpload::make('images')->view('fixcity::filament.forms.components.dc-file-upload')`.
    FUNZIONA: il campo resta il `SpatieMediaLibraryFileUpload` di Filament (stato `data.images`, regole di tipo/dimensione/numero, salvataggio su
    Media Library in `saveRelationships()`); cambia solo il client. Il tema non carica il CSS di FilePond, quindi il widget di Filament compariva
    senza stile (input nativo visibile). Qui un input `file` nascosto + Alpine con lo stesso contratto di Filament (`file-upload.js`):
    carica con `$wire.upload('<statePath>.<chiave>', file, ...)` (Livewire, file temporaneo), annulla con `$wire.cancelUpload(...)` e toglie un
    file caricato con `callSchemaComponentMethod(<key>, 'removeUploadedFile', { fileKey })`.
    Controlli lato client = stesse regole del server (mime, dimensione massima, numero massimo: `getAcceptedFileTypes()`, `getMaxSize()`,
    `getMaxFiles()`); il server le applica comunque a "Avanti" e ne mostra gli errori sotto il campo. `wire:ignore` sul blocco interattivo: la
    lista vive nel client, un morph di Livewire non deve azzerarla; gli errori del server stanno fuori da `wire:ignore`.
    Nessun Bootstrap JS, solo classi Bootstrap Italia gia' nel riferimento (le miniature sono dimensionate da overrides.css).
    Due pulsanti (story Media `media-mobile-camera-upload`): "Scatta una foto" usa `capture`, che su iOS e Android apre solo la fotocamera;
    "Scegli file" non lo usa, cosi' restano galleria e PDF. La riduzione delle foto e' `window.MediaCameraImage` (modulo Media).
--}}
@php
    $id = $getId();
    $statePath = $getStatePath();
    $isDisabledField = $isDisabled();
    $maxFiles = $getMaxFiles() ?? 5;
    $maxKb = $getMaxSize();
    // DCP-378: il browser controlla il limite che il trasporto applica davvero (contratto Media: regola del campo, regola Livewire e 90% del
    // limite reale di PHP), non solo la regola del campo: altrimenti un file da 3 MB passa qui e fallisce con un errore generico.
    $maxBytes = app(\Modules\Media\Actions\Upload\GetEffectiveUploadLimitAction::class)->execute($maxKb ?: null);
    $accepted = array_values($getAcceptedFileTypes() ?? []);
    // HEIC/HEIF non vanno in `accept`: se dichiarati iOS consegna l'HEIC originale, altrimenti lo converte in JPEG da se'.
    // Un HEIC arrivato comunque (desktop, Android) lo converte il client; la whitelist server resta invariata.
    // lato lungo della foto ridotta: lo stesso che FilePond usa nel pannello admin (`CameraPhotoUpload`)
    $maxSide = (int) ($getAutomaticallyResizeImagesWidth() ?? \Modules\Media\Filament\Forms\Components\CameraPhotoUpload::MAX_SIDE);
    $acceptsImages = $accepted === [] || collect($accepted)->contains(static fn (string $mime): bool => str_starts_with($mime, 'image/'));
    $formatLabels = collect($accepted)
        ->map(static fn (string $mime): string => match ($mime) {
            'image/jpeg' => 'JPG',
            'image/png' => 'PNG',
            'application/pdf' => 'PDF',
            default => strtoupper((string) str($mime)->afterLast('/')),
        })
        ->implode(', ');
    $sprite = '/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg';
    $serverErrors = collect([$errors->first($statePath)])
        ->merge(collect($errors->get($statePath . '.*'))->flatten())
        ->filter()
        ->unique()
        ->values();
    $texts = [
        'type' => __('fixcity::ticket_form.fields.images.error_type'),
        'size' => __('fixcity::ticket_form.fields.images.error_size', ['max' => round($maxBytes / 1048576, 1)]),
        'count' => __('fixcity::ticket_form.fields.images.error_count', ['max' => $maxFiles]),
        'upload' => __('fixcity::ticket_form.fields.images.error_upload'),
        'prepare' => __('fixcity::ticket_form.fields.images.error_prepare'),
        'uploading' => __('fixcity::ticket_form.fields.images.uploading'),
        'preparing' => __('fixcity::ticket_form.fields.images.preparing'),
        'remove' => __('fixcity::ticket_form.fields.images.remove', ['name' => ':name']),
    ];
    $wrapperAttributes = $getExtraAttributeBag()
        ->merge($getExtraFieldWrapperAttributes(), escape: false)
        ->merge($serverErrors->isNotEmpty() ? ['data-field-wrapper' => ''] : [], escape: false)
        ->class(['btn-wrapper', 'px-3', 'pt-2', 'pb-3', 'px-lg-4', 'pb-lg-4', 'pt-lg-0', 'bg-white']);
@endphp
<div {{ $wrapperAttributes }}>
    <div
        wire:ignore
        x-data="{
            statePath: @js($statePath),
            componentKey: @js($getKey()),
            clientAccepted: @js($accepted),
            maxSide: @js($maxSide),
            maxFiles: @js($maxFiles),
            maxBytes: @js($maxBytes),
            texts: @js($texts),
            files: [],
            message: '',
            patch(key, props) {
                const item = this.files.find((file) => file.key === key)
                if (item) Object.assign(item, props)
            },
            isCameraFormat(file) {
                return window.MediaCameraImage?.isCameraFormat(file) ?? false
            },
            isAcceptedFile(file) {
                const type = (file.type || '').toLowerCase()
                const extension = (file.name || '').toLowerCase().split('.').pop()
                const mimeByExtension = { jpg: 'image/jpeg', jpeg: 'image/jpeg', png: 'image/png', pdf: 'application/pdf' }
                const normalizedType = type || mimeByExtension[extension] || ''

                return this.clientAccepted.includes(normalizedType) || ['jpg', 'jpeg', 'png', 'pdf'].includes(extension)
            },
            // DCP-378: una foto da fotocamera (3-8 MB) supera il limite di upload del server (2 MB): la riduce il modulo Media.
            // Senza bundle del tema (test, errore di rete) si invia l'originale e decide il server.
            async normalizeCameraImage(file) {
                return window.MediaCameraImage ? window.MediaCameraImage.normalize(file, { maxSide: this.maxSide }) : file
            },
            async pick(event) {
                const picked = [...event.target.files]
                event.target.value = ''
                this.message = ''
                for (const sourceFile of picked) {
                    if (this.files.length >= this.maxFiles) return void (this.message = this.texts.count)
                    if (this.clientAccepted.length && ! this.isAcceptedFile(sourceFile) && ! this.isCameraFormat(sourceFile)) { this.message = this.texts.type; continue }
                    // chiave senza crypto.randomUUID (assente fuori da un contesto sicuro, es. HTTP su IP di rete locale)
                    const key = 'f' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8)
                    // la riduzione dura 1-3 s su un telefono: la voce compare subito, non a lavoro finito
                    this.files.push({ key, name: sourceFile.name, status: 'preparing', progress: 0, preview: null })
                    const file = await this.normalizeCameraImage(sourceFile)
                    if (!this.files.some((item) => item.key === key)) continue
                    if (!file) { this.drop(key); this.message = this.texts.prepare; continue }
                    if (this.maxBytes && file.size > this.maxBytes) { this.drop(key); this.message = this.texts.size; continue }
                    this.send(file, key)
                }
            },
            send(file, key) {
                this.patch(key, { name: file.name, status: 'uploading', preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null })
                this.$wire.upload(
                    this.statePath + '.' + key,
                    file,
                    () => this.patch(key, { status: 'done', progress: 100 }),
                    () => { this.drop(key); this.message = this.texts.upload },
                    (progressEvent) => this.patch(key, { progress: progressEvent.detail.progress }),
                )
            },
            drop(key) {
                const item = this.files.find((file) => file.key === key)
                if (item?.preview) URL.revokeObjectURL(item.preview)
                this.files = this.files.filter((file) => file.key !== key)
            },
            async remove(item) {
                this.message = ''
                // una foto ancora in riduzione non e' partita: basta toglierla dalla lista (pick la salta)
                if (item.status === 'uploading') this.$wire.cancelUpload(this.statePath + '.' + item.key)
                else if (item.status === 'done') await this.$wire.callSchemaComponentMethod(this.componentKey, 'removeUploadedFile', { fileKey: item.key })
                this.drop(item.key)
            },
        }"
    >
        <span id="{{ $id }}-label" class="d-block title-xsmall-bold u-grey-dark pb-2 ms-2">{{ $getLabel() }}</span>
        <template x-for="item in files" :key="item.key">
            <div class="upload-wrapper d-flex justify-content-between align-items-center">
                <img x-show="item.preview" :src="item.preview" alt="" class="img">
                <svg x-show="! item.preview" class="icon icon-primary icon-sm" aria-hidden="true"><use href="{{ $sprite }}#it-file"></use></svg>
                <span class="t-primary fw-bold w-100 ms-2" x-text="item.status === 'preparing' ? item.name + ' - ' + texts.preparing : (item.status === 'uploading' ? item.name + ' - ' + texts.uploading + ' ' + item.progress + '%' : item.name)"></span>
                <button type="button" class="btn btn-link p-0 border-0 align-self-center" :aria-label="texts.remove.replace(':name', item.name)" x-on:click="remove(item)">
                    <svg class="icon icon-primary icon-sm mb-1" aria-hidden="true"><use href="{{ $sprite }}#it-close"></use></svg>
                </button>
            </div>
        </template>
        <hr x-show="files.length" x-cloak>
        {{-- input nascosti fuori dal Tab: si usano solo dai pulsanti, che hanno testo visibile (WCAG 2.5.3) --}}
        @if ($acceptsImages)
            <input type="file" id="{{ $id }}-camera" x-ref="camera" class="visually-hidden" tabindex="-1" aria-hidden="true" accept="image/*" capture="environment" @disabled($isDisabledField) x-on:change="pick($event)">
        @endif
        <input
            type="file"
            id="{{ $id }}"
            x-ref="input"
            class="visually-hidden"
            tabindex="-1"
            aria-hidden="true"
            multiple
            @if ($accepted !== []) accept="{{ implode(',', $accepted) }}" @endif
            @disabled($isDisabledField)
            x-on:change="pick($event)"
        >
        <div role="group" aria-labelledby="{{ $id }}-label" class="d-flex flex-column gap-2">
            @if ($acceptsImages)
                <button type="button" class="btn btn-primary w-100 fw-bold" aria-describedby="{{ $id }}-help" @disabled($isDisabledField) x-on:click="$refs.camera.click()">
                    <span class="rounded-icon">
                        <svg class="icon icon-white icon-sm" aria-hidden="true"><use href="{{ $sprite }}#it-camera"></use></svg>
                    </span>
                    <span>{{ __('fixcity::ticket_form.fields.images.camera_button') }}</span>
                </button>
            @endif
            <button type="button" @class(['btn w-100 fw-bold', 'btn-outline-primary' => $acceptsImages, 'btn-primary' => ! $acceptsImages]) aria-describedby="{{ $id }}-help" @disabled($isDisabledField) x-on:click="$refs.input.click()">
                <span class="rounded-icon">
                    <svg @class(['icon icon-sm', 'icon-primary' => $acceptsImages, 'icon-white' => ! $acceptsImages]) aria-hidden="true"><use href="{{ $sprite }}#it-upload"></use></svg>
                </span>
                <span>{{ __('fixcity::ticket_form.fields.images.upload_button') }}</span>
            </button>
        </div>
        <p id="{{ $id }}-help" class="title-xsmall u-grey-dark pt-10 mb-0">{{ __('fixcity::ticket_form.fields.images.helper_text', ['formats' => $formatLabels, 'max_files' => $maxFiles, 'max_size' => $maxKb ? round($maxKb / 1024, 1) : 10]) }}</p>
        <p class="form-feedback just-validate-error-label" role="status" x-show="message" x-text="message" x-cloak></p>
    </div>
    @foreach ($serverErrors as $message)
        <p id="{{ $id }}-error-{{ $loop->index }}" data-validation-error class="form-feedback just-validate-error-label">{{ $message }}</p>
    @endforeach
</div>

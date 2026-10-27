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
--}}
@php
    $id = $getId();
    $statePath = $getStatePath();
    $isDisabledField = $isDisabled();
    $maxFiles = $getMaxFiles() ?? 5;
    $maxKb = $getMaxSize();
    $accepted = array_values($getAcceptedFileTypes() ?? []);
    // iOS puo' consegnare alla fotocamera HEIC/HEIF: il picker li mostra solo per
    // convertirli in JPEG nel browser; la whitelist server resta invariata.
    $clientAccepted = array_values(array_unique([...$accepted, 'image/heic', 'image/heif']));
    // l'aiuto elenca solo cio' che il server accetta davvero (`$accepted`), non i formati HEIC/HEIF che il client converte dove il browser li sa leggere
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
        'size' => __('fixcity::ticket_form.fields.images.error_size', ['max' => $maxKb ? round($maxKb / 1024, 1) : 10]),
        'count' => __('fixcity::ticket_form.fields.images.error_count', ['max' => $maxFiles]),
        'upload' => __('fixcity::ticket_form.fields.images.error_upload'),
        'uploading' => __('fixcity::ticket_form.fields.images.uploading'),
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
            clientAccepted: @js($clientAccepted),
            maxFiles: @js($maxFiles),
            maxBytes: @js($maxKb ? $maxKb * 1024 : null),
            texts: @js($texts),
            files: [],
            message: '',
            patch(key, props) {
                const item = this.files.find((file) => file.key === key)
                if (item) Object.assign(item, props)
            },
            isCameraFormat(file) {
                return ['image/heic', 'image/heif'].includes((file.type || '').toLowerCase()) || /\.(heic|heif)$/i.test(file.name || '')
            },
            isAcceptedFile(file) {
                const type = (file.type || '').toLowerCase()
                const extension = (file.name || '').toLowerCase().split('.').pop()
                const mimeByExtension = { jpg: 'image/jpeg', jpeg: 'image/jpeg', png: 'image/png', pdf: 'application/pdf' }
                const normalizedType = type || mimeByExtension[extension] || ''

                return this.clientAccepted.includes(normalizedType) || ['jpg', 'jpeg', 'png', 'pdf'].includes(extension)
            },
            async normalizeCameraImage(file) {
                if (!this.isCameraFormat(file)) return file

                if (window.FixcityCameraUpload?.toJpeg) {
                    try {
                        return await window.FixcityCameraUpload.toJpeg(file)
                    } catch (error) {
                        // Some browsers can decode HEIC natively even when the
                        // converter is unavailable; keep that path as fallback.
                    }
                }

                const source = URL.createObjectURL(file)
                try {
                    const image = new Image()
                    image.src = source
                    await image.decode()

                    const canvas = document.createElement('canvas')
                    canvas.width = image.naturalWidth
                    canvas.height = image.naturalHeight
                    const context = canvas.getContext('2d')
                    if (!context || !canvas.width || !canvas.height) return null
                    context.drawImage(image, 0, 0)

                    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.92))
                    if (!(blob instanceof Blob)) return null

                    return new File([blob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), {
                        type: 'image/jpeg',
                        lastModified: file.lastModified,
                    })
                } catch {
                    return null
                } finally {
                    URL.revokeObjectURL(source)
                }
            },
            async pick(event) {
                const picked = [...event.target.files]
                event.target.value = ''
                this.message = ''
                for (const sourceFile of picked) {
                    if (this.files.length >= this.maxFiles) return void (this.message = this.texts.count)
                    if (this.clientAccepted.length && ! this.isAcceptedFile(sourceFile) && ! this.isCameraFormat(sourceFile)) { this.message = this.texts.type; continue }
                    const file = await this.normalizeCameraImage(sourceFile)
                    if (!file) { this.message = this.texts.upload; continue }
                    if (this.maxBytes && file.size > this.maxBytes) { this.message = this.texts.size; continue }
                    this.send(file)
                }
            },
            send(file) {
                // chiave senza crypto.randomUUID (assente fuori da un contesto sicuro, es. HTTP su IP di rete locale)
                const key = 'f' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8)
                this.files.push({ key, name: file.name, status: 'uploading', progress: 0, preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null })
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
                if (item.status === 'uploading') this.$wire.cancelUpload(this.statePath + '.' + item.key)
                else await this.$wire.callSchemaComponentMethod(this.componentKey, 'removeUploadedFile', { fileKey: item.key })
                this.drop(item.key)
            },
        }"
    >
        <label class="title-xsmall-bold u-grey-dark pb-2 ms-2" for="{{ $id }}">{{ $getLabel() }}</label>
        <template x-for="item in files" :key="item.key">
            <div class="upload-wrapper d-flex justify-content-between align-items-center">
                <img x-show="item.preview" :src="item.preview" alt="" class="img">
                <svg x-show="! item.preview" class="icon icon-primary icon-sm" aria-hidden="true"><use href="{{ $sprite }}#it-file"></use></svg>
                <span class="t-primary fw-bold w-100 ms-2" x-text="item.status === 'uploading' ? item.name + ' - ' + texts.uploading + ' ' + item.progress + '%' : item.name"></span>
                <button type="button" class="btn btn-link p-0 border-0 align-self-center" :aria-label="texts.remove.replace(':name', item.name)" x-on:click="remove(item)">
                    <svg class="icon icon-primary icon-sm mb-1" aria-hidden="true"><use href="{{ $sprite }}#it-close"></use></svg>
                </button>
            </div>
        </template>
        <hr x-show="files.length" x-cloak>
        <input
            type="file"
            id="{{ $id }}"
            x-ref="input"
            class="visually-hidden"
            multiple
            @if ($clientAccepted !== []) accept="{{ implode(',', $clientAccepted) }}" @endif
            capture="environment"
            @disabled($isDisabledField)
            aria-describedby="{{ $id }}-help"
            x-on:change="pick($event)"
        >
        <button type="button" aria-label="{{ __('fixcity::ticket_form.fields.images.upload_aria') }}" class="btn btn-primary w-100 fw-bold" @disabled($isDisabledField) x-on:click="$refs.input.click()">
            <span class="rounded-icon">
                <svg class="icon icon-white icon-sm" aria-hidden="true"><use href="{{ $sprite }}#it-upload"></use></svg>
            </span>
            <span>{{ __('fixcity::ticket_form.fields.images.upload_button') }}</span>
        </button>
        <p id="{{ $id }}-help" class="title-xsmall u-grey-dark pt-10 mb-0">{{ __('fixcity::ticket_form.fields.images.helper_text', ['formats' => $formatLabels, 'max_files' => $maxFiles, 'max_size' => $maxKb ? round($maxKb / 1024, 1) : 10]) }}</p>
        <p class="form-feedback just-validate-error-label" role="status" x-show="message" x-text="message" x-cloak></p>
    </div>
    @foreach ($serverErrors as $message)
        <p id="{{ $id }}-error-{{ $loop->index }}" data-validation-error class="form-feedback just-validate-error-label">{{ $message }}</p>
    @endforeach
</div>

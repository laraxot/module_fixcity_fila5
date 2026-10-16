<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\CreateCommentAction;
use Modules\Fixcity\Models\Ticket;

/**
 * Widget per commenti moderatori con upload foto.
 *
 * Il widget è pensato per essere inserito nella pagina dettaglio ticket
 * e permette di:
 * - inserire testo del commento
 * - caricare foto (solo tipo immagine)
 * - segnare come "private_note" (visibile solo a moderatore/dipartimento/admin)
 * - creare automaticamente un TimelineStep::photo_added quando si carica una foto
 *
 * Segue il pattern Commentable/CommentWidget per modelli che implementano Commentable.
 */
class CommentWidget extends Widget
{
    protected string $view = 'fixcity::filament.widgets.comment-widget';

    protected int|string|array $columnSpan = 'full';

    protected ?array $data = [];

    protected bool $isPrivateNote = false;

    protected bool $showUploadError = false;

    protected ?string $uploadError = '';

    protected ?string $uploadedFilePath = '';

    protected ?int $uploadedFileId = null;

    protected ?array $uploadedFile = [];

    protected ?string $commentText = '';

    protected ?array $commentData = [];

    protected ?int $ticketId = null;

    protected ?int $userId = null;

    protected ?string $action = 'create';

    /**
     * @return array<string, mixed>
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFile ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getCommentData(): array
    {
        return $this->commentData ?? [];
    }

    public function getCommentText(): string
    {
        return $this->commentText ?? '';
    }

    /**
     * @return array<string, mixed>
     */
    public function getUploadError(): array
    {
        return $this->showUploadError ? [$this->uploadError] : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getPrivateNote(): array
    {
        return [$this->isPrivateNote];
    }

    /**
     * @return array<string, mixed>
     */
    public function getAction(): array
    {
        return [$this->action];
    }

    /**
     * @param  int|string|array  $columnSpan
     */
    public function columnSpan($columnSpan): static
    {
        $this->columnSpan = $columnSpan;

        return $this;
    }

    /**
     * @param  int|string|array  $columnSpan
     */
    public function columnSpanClass($class): static
    {
        $this->columnSpanClass = $class;

        return $this;
    }

    public function action(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param int|string|? $ticketId
     */
    public function ticketId($ticketId): static
    {
        $this->ticketId = $ticketId;

        return $this;
    }

    /**
     * @param int|string|? $userId
     */
    public function userId($userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function commentText(string $commentText): static
    {
        $this->commentText = $commentText;

        return $this;
    }

    public function commentData(array $commentData): static
    {
        $this->commentData = $commentData;

        return $this;
    }

    public function privateNote(bool $isPrivateNote): static
    {
        $this->isPrivateNote = $isPrivateNote;

        return $this;
    }

    public function uploadError(string $uploadError): static
    {
        $this->uploadError = $uploadError;
        $this->showUploadError = true;

        return $this;
    }

    public function uploadedFilePath(string $uploadedFilePath): static
    {
        $this->uploadedFilePath = $uploadedFilePath;

        return $this;
    }

    public function uploadedFileId(?int $uploadedFileId): static
    {
        $this->uploadedFileId = $uploadedFileId;

        return $this;
    }

    public function uploadedFile(?array $uploadedFile): static
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $data = [
            'ticket_id' => $this->ticketId,
            'user_id' => $this->userId,
            'is_private_note' => $this->isPrivateNote,
            'show_upload_error' => $this->showUploadError,
            'upload_error' => $this->uploadError,
            'uploaded_file_path' => $this->uploadedFilePath,
            'uploaded_file_id' => $this->uploadedFileId,
            'uploaded_file' => $this->uploadedFile,
            'comment_text' => $this->commentText,
            'comment_data' => $this->commentData,
            'action' => $this->action,
        ];

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSchema(): array
    {
        return [
            'action' => [
                'type' => 'select',
                'options' => [
                    'create' => 'Crea nuovo commento',
                    'reply' => 'Rispondi a commento',
                    'edit' => 'Modifica commento',
                    'delete' => 'Elimina commento',
                ],
                'default' => 'create',
                'optionsAsString' => false,
                'allowClear' => false,
            ],

            'ticket_id' => [
                'type' => 'text',
                'label' => __('fixcity::ticket.detail.title'),
                'placeholder' => __('fixcity::ticket.detail.placeholder'),
                'default' => $this->ticketId ?? '',
                'readOnly' => $this->action !== 'create',
                'disabled' => true,
            ],

            'user_id' => [
                'type' => 'text',
                'label' => __('fixcity::user.profile.title'),
                'placeholder' => __('fixcity::user.profile.placeholder'),
                'default' => $this->userId ?? '',
                'readOnly' => $this->action !== 'create',
                'disabled' => true,
            ],

            'comment_text' => [
                'type' => 'rich-editor',
                'label' => __('fixcity::comment.text'),
                'default' => $this->commentText ?? '',
                'placeholder' => __('fixcity::comment.placeholder'),
                'downcast' => fn ($value) => $value ?? '',
                'required' => true,
            ],

            'is_private_note' => [
                'type' => 'checkbox',
                'label' => __('fixcity::comment.private_note'),
                'default' => $this->isPrivateNote,
                'help' => __('fixcity::comment.private_note_help'),
            ],

            'file_upload' => [
                'type' => 'file-upload',
                'label' => __('fixcity::comment.photo_upload'),
                'rules' => [
                    'required',
                    'image',
                    'max:5120', // 5MB max
                    'dimensions:min_width=300',
                    'dimensions:min_height=200',
                ],
                'upload' => [
                    'disk' => 'public', // Use public disk for accessibility
                    'visibility' => 'public',
                    'file_name' => 'auto',
                    'mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
                ],
                'default' => $this->uploadedFile ?? '',
                'clearable' => true,
                'show_on_create' => true,
                'show_on_edit' => true,
                'required' => false,
                'disabled' => $this->action !== 'create',
            ],

            'upload_error' => [
                'type' => 'text',
                'label' => __('fixcity::comment.upload_error'),
                'default' => '',
                'readOnly' => true,
                'disabled' => true,
            ],
        ];
    }

    protected function mount(): void
    {
        // Carica dati iniziali se necessario
        $this->prepareInitialData();
    }

    protected function prepareInitialData(): void
    {
        // Inizializza dati di default
        $this->isPrivateNote = false;
        $this->showUploadError = false;
        $this->uploadError = '';
        $this->uploadedFilePath = '';
        $this->uploadedFileId = null;
        $this->uploadedFile = [];
        $this->commentText = '';
        $this->commentData = [];
        $this->action = 'create';
        $this->ticketId = null;
        $this->userId = null;
    }

    protected function saved(): void
    {
        // Gestione upload foto
        if ($this->uploadedFile && $this->action === 'create') {
            $this->handleFileUpload();
        }
    }

    protected function handleFileUpload(): void
    {
        try {
            $file = $this->uploadedFile[0] ?? null;

            if (! $file) {
                $this->uploadError = __('fixcity::comment.upload_error_no_file');
                $this->showUploadError = true;

                return;
            }

            // Validazione base
            if (! $file->isImage()) {
                $this->uploadError = __('fixcity::comment.upload_error_not_image');
                $this->showUploadError = true;

                return;
            }

            if ($file->size > 5120) { // 5MB
                $this->uploadError = __('fixcity::comment.upload_error_file_size');
                $this->showUploadError = true;

                return;
            }

            // Genera path univoco
            $filename = Str::uuid().'.'.$file->extension();
            $path = $file->storeAs('comment-photos/'.$this->ticketId, $filename, 'public');

            // Salva info file
            $this->uploadedFilePath = $path;
            $this->uploadedFileId = $file->hash; // Usa hash come ID
            $this->uploadedFile = [
                'path' => $path,
                'original_name' => $file->fileName(),
                'mime_type' => $file->mimeType(),
                'size' => $file->size(),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ];

            // Aggiorna commento dati
            $this->commentData = [
                'uploaded_file_id' => $this->uploadedFileId,
                'file_path' => $path,
                'original_name' => $file->fileName(),
                'mime_type' => $file->mimeType(),
                'size' => $file->size(),
                'uploaded_at' => now(),
            ];

            // Aggiorna testo commento se necessario
            $this->commentText = $this->commentText ?? 'Commento con foto';
        } catch (\Exception $e) {
            $this->uploadError = __('fixcity::comment.upload_error_generic');
            $this->showUploadError = true;
        }
    }

    protected function sending(): void
    {
        // Prepara dati per l'azione
        $data = $this->getViewData();

        // Gestione upload foto se presente
        if ($this->uploadedFile && $this->action === 'create') {
            $this->handleFileUpload();
        }

        // Passa dati all'azione
        $this->setData($data);
    }

    public function validateComment(): void
    {
        $this->validate([
            'commentText' => ['required', 'string'],
        ]);
    }

    protected function submit(): void
    {
        $this->validateComment();

        if ($this->errors()->isEmpty()) {
            $data = $this->getViewData();

            // Crea commento tramite azione
            $comment = CreateCommentAction::execute([
                'ticket_id' => $this->ticketId,
                'user_id' => $this->userId,
                'content' => $this->commentText,
                'is_private_note' => $this->isPrivateNote,
                'data' => $this->commentData ?? [],
            ]);

            // Aggiorna stato del widget
            $this->commentText = '';
            $this->commentData = [];
            $this->uploadedFile = [];
            $this->uploadedFilePath = '';
            $this->uploadedFileId = null;
            $this->showUploadError = false;
            $this->uploadError = '';

            // Aggiorna azione
            $this->action = 'create';
        }
    }

    protected function updated(): void
    {
        // Reset errori quando si modifica
        if ($this->action === 'create' || $this->action === 'reply') {
            $this->showUploadError = false;
            $this->uploadError = '';
        }
    }
}

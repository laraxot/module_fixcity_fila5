<?php

/**
 * @see https://filamentphp.com/docs/3.x/forms/adding-a-form-to-a-livewire-component#adding-the-form
 */

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Events\TicketCreatedEvent;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

/**
 * @property Schema $form
 */
class CreateTicketWidget extends XotBaseWidget
{
    /** @var array<string, mixed>|null */
    public ?array $data = [];

    protected string $view = 'fixcity::filament.widgets.create-ticket';

    protected int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        if (! auth()->check()) {
            $this->redirect('/login');

            return;
        }

        $this->form->fill([
            'status' => TicketStatusEnum::PENDING->value,
        ]);
    }

    /**
     * @return array<string, Component>
     */
    public function getFormSchema(): array
    {
        $privacyPolicy = app(GetPublishedPrivacyPolicyAction::class)->execute();

        return [
            'wizard' => Wizard::make([
                Step::make('step-1')
                    ->label(__('fixcity::fixcity.ticket.steps.auth.label'))
                    ->icon('heroicon-o-shield-check')
                    ->description(__('fixcity::fixcity.ticket.steps.auth.description'))
                    ->schema([
                        RichEditor::make('privacy_notice')
                            ->label('')
                            ->default($privacyPolicy === null
                                ? __('fixcity::ticket.privacy.not_configured')
                                : (string) Str::markdown($privacyPolicy, [
                                    'html_input' => 'strip',
                                    'allow_unsafe_links' => false,
                                ]))
                            ->disabled()
                            ->extraAttributes(['class' => 'border-0 shadow-none !p-0 !bg-transparent']),

                        Checkbox::make('accept_terms')
                            ->label(__('fixcity::fixcity.ticket.fields.accept_terms.label'))
                            ->helperText(__('fixcity::fixcity.ticket.fields.accept_terms.helper'))
                            ->required()
                            ->disabled($privacyPolicy === null)
                            ->default(false)
                            ->extraAttributes(['class' => 'text-green-500 text-lg checked:bg-green-500 checked:hover:bg-green-500 focus:ring-green-500'])
                            ->rules(['accepted'])
                            ->afterStateUpdated(function (mixed $state): void {
                                if (! $state) {
                                    $this->addError('data.accept_terms', __('fixcity::fixcity.ticket.validation.accept_terms'));
                                }
                            }),
                    ]),

                Step::make('step-2')
                    ->label(__('fixcity::fixcity.ticket.steps.data.label'))
                    ->icon('heroicon-o-document-text')
                    ->description(__('fixcity::fixcity.ticket.steps.data.description'))
                    ->schema([
                        'issue_heading' => Text::make(
                            new HtmlString(
                                '<h1 class="subtitle text-4xl font-bold mb-4 dark:text-white">'
                                .e(__('fixcity::fixcity.ticket.fields.issue.label'))
                                .'</h1>'
                            )
                        )->columnSpanFull(),
                        ...app(TicketResource::class)->getFormSchema(),
                    ]),
            ])
                ->nextAction(
                    static fn (Action $action) => $action
                        ->label(__('fixcity::fixcity.ticket.actions.next.label'))
                        ->icon('heroicon-m-arrow-right')
                        ->color('primary')
                        ->size('xl')
                        ->extraAttributes(['class' => 'px-8 py-4 text-lg font-bold w-96 -ml-6'])
                )
                ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <button
                        type="submit"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-btn-size-md gap-1.5 px-8 py-4 text-md font-bold w-64 bg-white text-green-600 border-2 border-green-600 hover:bg-green-50"
                    >
                        {{ __('fixcity::fixcity.ticket.actions.save.label') }}
                    </button>
                BLADE)))
                ->columnSpanFull()
                ->extraAttributes([
                    'class' => 'w-full max-w-full mx-auto',
                    'navigationContainerAttributes' => 'class="justify-start"',
                ]),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    /**
     * Compatibility entrypoint for the legacy widget tests and templates.
     * The canonical public flow uses CreateTicketWizardWidget, but this widget
     * remains usable and delegates persistence to the module Action.
     */
    /**
     * @return array<string, list<string|In>>
     */
    protected function rules(): array
    {
        return [
            'data.name' => ['required', 'string', 'min:3', 'max:255'],
            'data.accept_terms' => ['accepted'],
            'data.content' => ['required', 'string', 'min:10'],
            'data.type' => ['nullable', Rule::in(array_map(static fn (TicketTypeEnum $type): string => $type->value, TicketTypeEnum::cases()))],
            'data.priority' => ['nullable', Rule::in(array_map(static fn (TicketPriorityEnum $priority): string => $priority->value, TicketPriorityEnum::cases()))],
            'data.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'data.longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function submit(): void
    {
        if (! auth()->check()) {
            $this->redirect('/login');

            return;
        }

        if (app(GetPublishedPrivacyPolicyAction::class)->execute() === null) {
            $validator = Validator::make(['data' => $this->data ?? []], $this->rules());

            foreach ($validator->errors()->messages() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }

            $this->addError('data.accept_terms', __('fixcity::ticket.privacy.not_configured'));

            return;
        }

        if (($this->data['content'] ?? null) === '') {
            $this->addError('data.content', __('validation.required', ['attribute' => 'content']));

            return;
        }

        $this->validate();

        /** @var array<string, mixed> $data */
        $data = $this->data ?? [];
        $data['owner_id'] = auth()->id();
        app(CreateTicketAction::class)->execute($data);

        $this->redirect('/');
    }

    public function create(): void
    {
        if (app(GetPublishedPrivacyPolicyAction::class)->execute() === null) {
            $this->addError('data.accept_terms', __('fixcity::ticket.privacy.not_configured'));

            return;
        }

        // Ottieni i dati dal form
        $data = $this->form->getState();

        // Crea il ticket senza le immagini
        $ticket = Ticket::create($data);

        // Salva le immagini usando saveRelationships()
        $this->form->model($ticket)->saveRelationships();

        // Dispatch dell'evento
        TicketCreatedEvent::dispatch($ticket);

        // Redirect alla pagina principale
        redirect('/');
    }
}

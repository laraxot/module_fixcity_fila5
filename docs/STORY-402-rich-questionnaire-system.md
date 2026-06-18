---
title: "STORY-402: Rich Questionnaire System per Tipologie"
type: story
tags: [fixcity, questionnaire, dynamic-forms, ticket-types, validation, seeclickfix]
status: draft
priority: critical
assignee: TBD
created: 2026-06-17
updated: 2026-06-17
---

# STORY-402: Rich Questionnaire System per Tipologie

## User Story

**Come** amministratore del sistema  
**Voglio** configurare questionari dinamici per ogni tipologia di segnalazione (buche, rifiuti, illuminazione, ecc.)  
**Per** raccogliere dati strutturati e specifici che aiutino l'operatore a comprendere e prioritizzare il problema

## Background & Motivazione

**Gap Analysis Competitor:**
- **SeeClickFix** ha un sistema Q&A sofisticato: text, textarea, select, multivaluelist, number, datetime, boolean, note
- **Degradozero** ha livelli di urgenza (alta/media/bassa) con marker colorati sulla mappa
- **Fixcity attuale**: solo campi generici (name, content, location, status, type) - nessuna raccolta dati strutturata

**Benefici:**
1. **Routing automatico**: In base alle risposte, il sistema può assegnare automaticamente al reparto corretto
2. **Prioritizzazione**: Urgenza determinata da risposte (es: "buca > 50cm" = alta priorità)
3. **SLA differenziati**: In base alla criticità
4. **Analytics**: Dati aggregabili per trend (es: quante buche > 50cm nel quartiere X)

## Acceptance Criteria

### AC1: Question Types Supportati
```gherkin
Dato un amministratore
Quando crea un questionario per tipologia "buche_stradali"
Allora può aggiungere campi di tipo:
  - text (short answer)
  - textarea (long description)
  - select (single choice dropdown)
  - multiselect (multiple choice checkboxes)
  - number (integer o float con min/max)
  - boolean (yes/no toggle)
  - datetime (date picker)
  - rating (1-5 stars)
  - note (info text no input)
  - media (foto/video attachment)
```

### AC2: Configurazione Campi
```gherkin
Dato un campo di tipo "select"
Quando lo configuro
Allora posso specificare:
  - label (string, required)
  - key (string, snake_case, unique nel questionario)
  - options (array di {value, label, color?, icon?})
  - required (boolean)
  - default_value (mixed)
  - help_text (string, nullable)
  - placeholder (string)
  - validation_rules (array: min, max, regex, ecc.)
  - conditional_logic (mostra se altro campo = valore X)
  - impact_on_priority (low/medium/high/critical)
```

### AC3: UI Dinamica Frontend
```gherkin
Dato un cittadino che crea segnalazione
Quando seleziona tipologia "buche_stradali"
Allora il form mostra dinamicamente:
  - "Dimensione della buca" (select: <30cm, 30-50cm, >50cm)
  - "Pericolo immediato" (boolean: yes/no)
  - "Posizione" (select: carreggiata, marciapiede, pista ciclabile)
  - "Note aggiuntive" (textarea, optional)
E il marker sulla mappa cambia colore in base a urgenza calcolata
```

### AC4: Validazione Server-side
```gherkin
Dato un submit di segnalazione con questionario
Quando i dati arrivano al backend
Allora viene validato:
  - Tutti i campi required sono presenti
  - I campi number sono nel range min-max
  - I campi select hanno value valida
  - I campi text rispettano regex se specificata
  - Conditional logic è soddisfatta (campi nascosti non validati)
```

### AC5: Calcolo Automatico Urgenza
```gherkin
Dato un questionario compilato
Quando il sistema processa le risposte
Allora calcola automaticamente:
  - priority_score (1-100)
  - urgency_level (low|medium|high|critical)
  - estimated_resolution_time (hours)
  - auto_assign_department (id reparto)
E aggiorna il ticket con questi valori
```

### AC6: Admin Configuration Panel
```gherkin
Dato un amministratore
Quando accede a "/admin/questionnaires"
Allora può:
  - Vedere lista questionari per tipologia
  - Creare nuovo questionario con drag-and-drop builder
  - Clonare questionario esistente
  - Attivare/disattivare questionario per tipologia
  - Vedere preview del form
  - Esportare/importare configurazione JSON
```

## Technical Notes

### Database Schema

```php
// Migration: ticket_questionnaires
Schema::create('ticket_questionnaires', function (Blueprint $table) {
    $table->id();
    $table->string('ticket_type', 50); // matches TicketTypeEnum
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('version')->default(1);
    $table->json('scoring_rules'); // regole per calcolo priorità
    $table->timestamps();
    $table->index('ticket_type');
});

// Migration: ticket_questionnaire_fields
Schema::create('ticket_questionnaire_fields', function (Blueprint $table) {
    $table->id();
    $table->foreignId('questionnaire_id')->constrained('ticket_questionnaires')->cascadeOnDelete();
    $table->string('key', 100); // snake_case identifier
    $table->string('label');
    $table->string('type', 50); // text, textarea, select, number, boolean, datetime, rating, note, media
    $table->boolean('is_required')->default(false);
    $table->json('options')->nullable(); // for select/multiselect
    $table->json('validation_rules')->nullable(); // {min, max, regex, custom_message}
    $table->json('conditional_logic')->nullable(); // {field, operator, value}
    $table->string('impact_on_priority', 20)->default('none'); // none, low, medium, high, critical
    $table->integer('sort_order')->default(0);
    $table->json('ui_config')->nullable(); // {placeholder, help_text, icon, cols}
    $table->timestamps();
    $table->unique(['questionnaire_id', 'key']);
});

// Migration: ticket_answers (store user responses)
Schema::create('ticket_answers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
    $table->foreignId('questionnaire_field_id')->constrained('ticket_questionnaire_fields');
    $table->json('value'); // stored as JSON to handle different types
    $table->timestamps();
    $table->unique(['ticket_id', 'questionnaire_field_id']);
});
```

### Eloquent Models

```php
// app/Models/TicketQuestionnaire.php
class TicketQuestionnaire extends Model
{
    protected $fillable = ['ticket_type', 'name', 'slug', 'description', 'is_active', 'version', 'scoring_rules'];
    
    protected $casts = [
        'is_active' => 'boolean',
        'scoring_rules' => 'array',
    ];
    
    public function fields(): HasMany
    {
        return $this->hasMany(TicketQuestionnaireField::class, 'questionnaire_id')
            ->orderBy('sort_order');
    }
    
    public function ticketType(): TicketTypeEnum
    {
        return TicketTypeEnum::from($this->ticket_type);
    }
}

// app/Models/TicketQuestionnaireField.php
class TicketQuestionnaireField extends Model
{
    protected $fillable = [
        'questionnaire_id', 'key', 'label', 'type', 'is_required',
        'options', 'validation_rules', 'conditional_logic', 
        'impact_on_priority', 'sort_order', 'ui_config'
    ];
    
    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'validation_rules' => 'array',
        'conditional_logic' => 'array',
        'ui_config' => 'array',
    ];
    
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(TicketQuestionnaire::class, 'questionnaire_id');
    }
    
    public function getQuestionType(): QuestionTypeEnum
    {
        return QuestionTypeEnum::from($this->type);
    }
    
    public function shouldShow(array $formData): bool
    {
        if (empty($this->conditional_logic)) {
            return true;
        }
        
        $field = $this->conditional_logic['field'] ?? null;
        $operator = $this->conditional_logic['operator'] ?? 'equals';
        $value = $this->conditional_logic['value'] ?? null;
        
        if (!$field || !isset($formData[$field])) {
            return true;
        }
        
        $fieldValue = $formData[$field];
        
        return match($operator) {
            'equals' => $fieldValue == $value,
            'not_equals' => $fieldValue != $value,
            'contains' => is_array($fieldValue) && in_array($value, $fieldValue),
            'greater_than' => $fieldValue > $value,
            'less_than' => $fieldValue < $value,
            default => true,
        };
    }
}
```

### Enums

```php
// app/Enums/QuestionTypeEnum.php
enum QuestionTypeEnum: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case MULTISELECT = 'multiselect';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case DATETIME = 'datetime';
    case RATING = 'rating';
    case NOTE = 'note';
    case MEDIA = 'media';
    
    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Short Text',
            self::TEXTAREA => 'Long Text',
            self::SELECT => 'Single Choice',
            self::MULTISELECT => 'Multiple Choice',
            self::NUMBER => 'Number',
            self::BOOLEAN => 'Yes/No',
            self::DATETIME => 'Date & Time',
            self::RATING => 'Rating (1-5)',
            self::NOTE => 'Information Text',
            self::MEDIA => 'Media Attachment',
        };
    }
    
    public function hasOptions(): bool
    {
        return in_array($this, [self::SELECT, self::MULTISELECT], true);
    }
    
    public function castValue(mixed $value): mixed
    {
        return match($this) {
            self::NUMBER => (float) $value,
            self::BOOLEAN => (bool) $value,
            self::MULTISELECT => (array) $value,
            self::DATETIME => Carbon::parse($value),
            default => (string) $value,
        };
    }
}

// app/Enums/PriorityImpactEnum.php  
enum PriorityImpactEnum: string
{
    case NONE = 'none';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
    
    public function score(): int
    {
        return match($this) {
            self::NONE => 0,
            self::LOW => 10,
            self::MEDIUM => 25,
            self::HIGH => 50,
            self::CRITICAL => 100,
        };
    }
}
```

### Actions

```php
// app/Actions/Questionnaire/CalculateTicketPriorityAction.php
class CalculateTicketPriorityAction
{
    use QueueableAction;
    
    public function execute(Ticket $ticket): array
    {
        $questionnaire = TicketQuestionnaire::where('ticket_type', $ticket->type->value)
            ->where('is_active', true)
            ->first();
            
        if (!$questionnaire) {
            return [
                'priority_score' => 50, // default
                'urgency_level' => 'medium',
                'estimated_hours' => 72,
            ];
        }
        
        $answers = $ticket->answers()->with('questionnaireField')->get();
        $totalScore = 0;
        
        foreach ($answers as $answer) {
            $field = $answer->questionnaireField;
            $impact = PriorityImpactEnum::from($field->impact_on_priority);
            
            // Calcolo punteggio base sull'impatto del campo
            $fieldScore = $impact->score();
            
            // Bonus/malus in base al valore (es: buca > 50cm = +50)
            $fieldScore += $this->calculateValueBonus($field, $answer->value);
            
            $totalScore += $fieldScore;
        }
        
        // Normalizza 0-100
        $normalizedScore = min(100, max(0, $totalScore));
        
        return [
            'priority_score' => $normalizedScore,
            'urgency_level' => $this->scoreToUrgency($normalizedScore),
            'estimated_hours' => $this->scoreToEstimatedHours($normalizedScore),
        ];
    }
    
    private function calculateValueBonus(TicketQuestionnaireField $field, mixed $value): int
    {
        // Logica custom per calcolare bonus in base al valore specifico
        return match($field->key) {
            'buca_dimensione' => match($value) {
                '<30cm' => 0,
                '30-50cm' => 20,
                '>50cm' => 50,
                default => 0,
            },
            'pericolo_immediato' => $value ? 100 : 0,
            default => 0,
        };
    }
    
    private function scoreToUrgency(int $score): string
    {
        return match(true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            default => 'low',
        };
    }
    
    private function scoreToEstimatedHours(int $score): int
    {
        return match(true) {
            $score >= 80 => 4,   // 4 ore
            $score >= 60 => 24,  // 1 giorno
            $score >= 40 => 72,  // 3 giorni
            default => 168,      // 1 settimana
        };
    }
}

// app/Actions/Questionnaire/StoreQuestionnaireAnswersAction.php
class StoreQuestionnaireAnswersAction
{
    use QueueableAction;
    
    public function execute(
        Ticket $ticket, 
        array $answers,
        TicketQuestionnaire $questionnaire
    ): Ticket {
        return DB::transaction(function () use ($ticket, $answers, $questionnaire) {
            // Salva risposte
            foreach ($answers as $fieldKey => $value) {
                $field = $questionnaire->fields()->where('key', $fieldKey)->first();
                
                if (!$field) {
                    continue;
                }
                
                TicketAnswer::create([
                    'ticket_id' => $ticket->id,
                    'questionnaire_field_id' => $field->id,
                    'value' => $field->getQuestionType()->castValue($value),
                ]);
            }
            
            // Calcola e assegna priorità
            $priorityData = app(CalculateTicketPriorityAction::class)->execute($ticket);
            $ticket->priority_score = $priorityData['priority_score'];
            $ticket->urgency_level = $priorityData['urgency_level'];
            $ticket->estimated_resolution_hours = $priorityData['estimated_hours'];
            $ticket->save();
            
            return $ticket->fresh();
        });
    }
}
```

### Filament Admin Resources

```php
// app/Filament/Resources/TicketQuestionnaireResource.php
class TicketQuestionnaireResource extends XotBaseResource
{
    protected static ?string $model = TicketQuestionnaire::class;
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Info')
                ->schema([
                    Select::make('ticket_type')
                        ->options(TicketTypeEnum::class)
                        ->required(),
                    TextInput::make('name')->required(),
                    TextInput::make('slug')->required()->unique(),
                    Textarea::make('description'),
                    Toggle::make('is_active')->default(true),
                ]),
            
            Section::make('Fields')
                ->schema([
                    Repeater::make('fields')
                        ->relationship()
                        ->schema([
                            TextInput::make('key')->required(),
                            TextInput::make('label')->required(),
                            Select::make('type')
                                ->options(QuestionTypeEnum::class)
                                ->required()
                                ->live(),
                            Toggle::make('is_required'),
                            Textarea::make('options')
                                ->visible(fn ($get) => in_array($get('type'), ['select', 'multiselect']))
                                ->helperText('JSON format: [{"value": "x", "label": "Y"}]'),
                            Select::make('impact_on_priority')
                                ->options(PriorityImpactEnum::class)
                                ->default('none'),
                        ])
                        ->collapsible()
                        ->orderColumn('sort_order'),
                ]),
        ]);
    }
}
```

### Livewire Component (Public Form)

```php
// app/Livewire/TicketQuestionnaireForm.php
class TicketQuestionnaireForm extends Component
{
    public ?TicketTypeEnum $selectedType = null;
    public array $formData = [];
    public ?TicketQuestionnaire $questionnaire = null;
    
    public function updatedSelectedType(): void
    {
        $this->questionnaire = TicketQuestionnaire::where('ticket_type', $this->selectedType?->value)
            ->where('is_active', true)
            ->first();
            
        $this->formData = [];
        
        // Inizializza campi condizionali
        if ($this->questionnaire) {
            foreach ($this->questionnaire->fields as $field) {
                if (!$field->conditional_logic) {
                    $this->formData[$field->key] = $field->validation_rules['default'] ?? null;
                }
            }
        }
    }
    
    public function getVisibleFieldsProperty(): Collection
    {
        if (!$this->questionnaire) {
            return collect();
        }
        
        return $this->questionnaire->fields->filter(
            fn ($field) => $field->shouldShow($this->formData)
        );
    }
    
    public function submit(): void
    {
        $validated = $this->validate($this->buildValidationRules());
        
        // Crea ticket
        $ticket = Ticket::create([
            'type' => $this->selectedType,
            // ... altri campi
        ]);
        
        // Salva risposte
        app(StoreQuestionnaireAnswersAction::class)->execute(
            $ticket, 
            $this->formData,
            $this->questionnaire
        );
        
        $this->dispatch('ticket-created', ticketId: $ticket->id);
    }
    
    private function buildValidationRules(): array
    {
        $rules = [];
        
        foreach ($this->getVisibleFieldsProperty() as $field) {
            $fieldRules = [];
            
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            $fieldRules[] = match($field->type) {
                'number' => 'numeric',
                'boolean' => 'boolean',
                'email' => 'email',
                default => 'string',
            };
            
            if (isset($field->validation_rules['min'])) {
                $fieldRules[] = "min:{$field->validation_rules['min']}";
            }
            if (isset($field->validation_rules['max'])) {
                $fieldRules[] = "max:{$field->validation_rules['max']}";
            }
            if (isset($field->validation_rules['regex'])) {
                $fieldRules[] = "regex:{$field->validation_rules['regex']}";
            }
            
            $rules["formData.{$field->key}"] = $fieldRules;
        }
        
        return $rules;
    }
    
    public function render(): View
    {
        return view('livewire.ticket-questionnaire-form');
    }
}
```

## Definition of Done

- [ ] Database schema migrations create e testate
- [ ] Enums QuestionTypeEnum e PriorityImpactEnum implementati
- [ ] Models TicketQuestionnaire, TicketQuestionnaireField, TicketAnswer con relazioni
- [ ] Actions CalculateTicketPriorityAction e StoreQuestionnaireAnswersAction
- [ ] Filament Resource per amministrazione questionari
- [ ] Livewire component per form pubblico con conditional logic
- [ ] Validazione server-side per tutti i tipi di campo
- [ ] Calcolo automatico priorità basato su scoring rules
- [ ] Test unitari per tutti i tipi di campo
- [ ] Test feature per workflow completo (creazione → salvataggio → calcolo priorità)
- [ ] Documentazione per amministratori su come configurare questionari
- [ ] PHPStan level max passa

## Related Issues

- Epic: EPIC-004 Workflow Management
- Depends on: STORY-401 (Timeline & Audit Trail)
- Blocks: STORY-405 (Dashboard Analytics - usa dati questionario)

## Discussion Links

- GitHub Issue: `https://github.com/laraxot/fixcity/issues/402`
- GitHub Discussion: `https://github.com/laraxot/fixcity/discussions/402`

## Competitor References

- SeeClickFix API: https://dev.seeclickfix.com/v2/issues/reporting/
- Open311 GeoReport v2: http://wiki.open311.org/GeoReport_v2/

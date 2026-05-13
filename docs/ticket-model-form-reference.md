# Ticket Model & TicketForm — Reference

## Modello: `Modules\Fixcity\Models\Ticket`

### Colonne DB rilevanti per il wizard pubblico

| Colonna | Tipo DB | Cast modello | Note |
|---|---|---|---|
| `name` | `string` | — | obbligatorio, `$fillable` |
| `content` | `longText` | — | `$fillable` |
| `type_id` | `integer` nullable | `TicketTypeEnum::class` | `$fillable`; l'enum è `string` backed ma la colonna DB è `integer` — il cast gestisce la conversione |
| `priority` | `string` nullable | **nessun cast** | `$fillable`; stringa raw (es. `'medium'`), NON `priority_id` |
| `location` | `json` nullable | `'array'` | `$fillable`; chiavi: `latitude`, `longitude`, `address` |
| `latitude` | `decimal(20,18)` nullable | — | colonna legacy, usare `location` nel wizard |
| `longitude` | `decimal(20,18)` nullable | — | colonna legacy, usare `location` nel wizard |
| `slug` | `string` unique | — | `$fillable`; generato da `HasSlug` — NON esporre nel wizard form |
| `owner_id` | `foreignId` | — | impostato nel `submit()` del widget da `Auth::user()->id` |
| `status` | `string` nullable | `TicketStatusEnum::class` | impostato post-creazione, non nel wizard |

### Trappole comuni

- **`type_id` vs `type`**: la colonna DB si chiama `type_id` (integer). Esiste anche una colonna `type` (string) nella migration di update — è legacy. Il cast enum è su `type_id`.
- **`priority` vs `priority_id`**: la colonna `$fillable` è `priority` (stringa). Esiste anche `priority_id` (foreignId legacy non usata nel wizard). NON usare `priority_id` nel form.
- **`slug`**: gestito da `HasSlug` automaticamente al `create()`. Non mettere nel form (nemmeno hidden/dehydrated).

---

## TicketForm: `Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm`

### API pubblica

| Metodo | Scopo |
|---|---|
| `getSteps(): array<int, Step>` | Ritorna i 3 step del wizard (privacy, data, summary) |
| `getDefaultFormState(): array` | Stato iniziale per `$this->form->fill()` nel widget mount |
| `getPrivacySchema(): array` | Step 1: checkbox privacy, `dehydrated(false)` |
| `getDataSchema(): array` | Step 2: tutti i campi del ticket |
| `getSummarySchema(): array` | Step 3: riepilogo read-only con `TextEntry` |
| `getFormSchema(): array` | Ritorna `[]` — il wizard usa `getSteps()`, non `getFormSchema()` |
| `formatTicketType(mixed): string` | Helper: enum/string/int → label leggibile |
| `formatTicketPriority(mixed): string` | Helper: enum/string → label leggibile |

### Regole form schema (Laraxot)

- **MAI** `->label()`, `->placeholder()`, `->helperText()` — gestiti da `LangServiceProvider`
- **SEMPRE** `->hiddenLabel()` su ogni field (label viene da lang key automatica)
- **MAI** `->disabled()` per il riepilogo — usare `TextEntry` con `->state()`
- **MAI** passare un oggetto enum a `form->fill()` — usare `->value` (es. `TicketPriorityEnum::default()->value`)

### Step data: campi

```php
TextInput::make('name')->hiddenLabel()->columnSpanFull()->required()->maxLength(255)
Select::make('type_id')->hiddenLabel()->searchable()->options(TicketTypeEnum::class)->columnSpanFull()
Select::make('priority')->hiddenLabel()->searchable()->options(TicketPriorityEnum::class)->default(TicketPriorityEnum::default()->value)->columnSpanFull()
Textarea::make('content')->hiddenLabel()->rows(4)->columnSpanFull()
CoordinatePicker::make('location')->hiddenLabel()->columnSpanFull()->zoom(15)->height('340px')
SpatieMediaLibraryFileUpload::make('images')->hiddenLabel()->collection('attachments')->directory('attachments')->disk('uploads')->multiple()->maxFiles(5)->maxSize(10240)->columnSpanFull()
```

### Step summary: nomi entry

I `TextEntry` nel summary usano prefisso `review_` per evitare conflitti con i field del form nello stesso Livewire state:

```
review_type      → formatTicketType($get('type_id'))
review_priority  → formatTicketPriority($get('priority'))
review_name      → $get('name')
review_content   → $get('content')
review_location  → $get('location')['address'] ?? lat,lng
```

### getDefaultFormState()

```php
[
    'privacyAccepted' => false,
    'name' => '',
    'type_id' => null,
    'priority' => TicketPriorityEnum::default()->value,  // stringa, non enum
    'content' => '',
    'location' => ['latitude' => null, 'longitude' => null, 'address' => null],
]
```

Note: `images` NON in `getDefaultFormState()` — `SpatieMediaLibraryFileUpload` gestisce il proprio stato interno.

---

## CreateTicketWizardWidget: mount()

```php
public function mount(array $blockData = []): void
{
    $this->blockData = $blockData;
    $this->wizardStartStep = 1;
    $this->form->fill(TicketForm::getDefaultFormState());
}
```

Il `form->fill()` DEVE essere chiamato nel `mount()` per inizializzare il Livewire state e prevenire errori `Livewire Entangle`.

---

## Riferimenti

- `Modules/Fixcity/app/Models/Ticket.php`
- `Modules/Fixcity/database/migrations/2026_04_29_110000_create_tickets_table.php`
- `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- `Modules/Fixcity/docs/filament-summary-infolist-guidance.md`
- `Modules/Fixcity/docs/filament-wizard-pattern.md`

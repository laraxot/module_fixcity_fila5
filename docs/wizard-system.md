# Wizard System Documentation

## Overview
The wizard system provides a multi-step form interface for creating segnalazioni (service reports) in the Fixcity application. It uses a step-by-step approach to guide users through the ticket creation process, improving user experience and data quality.

## Architecture

### Wizard Components

#### 1. CreateTicketWizardWidget
```php
class CreateTicketWizardWidget extends FilamentWidget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    
    // Multi-step form logic
    public int $currentStep = 1;
    
    // Form data management
    public array $blockData = [];
    
    // View rendering
    protected string $view = 'filament.widgets.create-ticket-wizard';
}
```

#### 2. TicketForm Schema
```php
class TicketForm
{
    public static function getSteps(): array
    {
        return [
            Step::make('privacy')->schema(self::getPrivacySchema()),
            Step::make('data')->schema(self::getDataSchema()),
            Step::make('summary')->schema(self::getSummarySchema()),
        ];
    }
}
```

#### 3. View Template
```blade
<!-- Wizard container with responsive layout -->
<div class="segnalazione-wizard-container" data-wizard-step="{{ $currentStep }}">
    <div class="container">
        <div class="row">
            <!-- Sidebar for data step -->
            @if($isDataStep)
                <div class="col-lg-3">
                    @include('pub_theme::components.wizard.sidebar')
                </div>
            @endif
            
            <!-- Main content -->
            <div class="col-12 {{ $isDataStep ? 'col-lg-9' : 'col-lg-8' }}">
                <x-filament-widgets::widget>
                    {{ $this->form }}
                </x-filament-widgets::widget>
            </div>
        </div>
    </div>
</div>
```

## Workflow

### Step-by-Step Process

#### Step 1: Privacy Acceptance
- **Purpose**: User acceptance of privacy terms
- **Form Fields**: 
  - `data.privacyAccepted`: Checkbox for privacy acceptance
- **Validation**: Required acceptance
- **Navigation**: Next button to data step

#### Step 2: Data Collection
- **Purpose**: Collect ticket information and location
- **Form Fields**:
  - `data.name`: Reporter name
  - `data.type`: Ticket type
  - `data.priority`: Priority level
  - `data.content`: Description
  - `data.location`: Location data
- **Validation**: Required fields validation
- **Navigation**: Previous/Next buttons

#### Step 3: Summary
- **Purpose**: Review and submit
- **Form Fields**: Display of collected data
- **Actions**: Submit button
- **Navigation**: Final submission

### State Management

#### Current Step Tracking
```php
public function getCurrentStepIndex(): int
{
    return $this->currentStep - 1;
}

public function goToStep(int $step): void
{
    $maxSteps = 2; // privacy and data
    if ($step >= 1 && $step <= $maxSteps) {
        $this->currentStep = $step;
    }
}
```

#### Form Data Persistence
```php
public function form($form)
{
    return $form
        ->schema(match ($this->currentStep) {
            1 => TicketForm::getPrivacySchema(),
            2 => TicketForm::getDataSchema(),
            default => [],
        })
        ->statePath('data');
}
```

## Configuration

### Environment Variables
```env
# Confirmation page slug
FIXCITY_WIZARD_CONFIRMATION_SLUG=segnalazione-04-conferma

# Google Maps API for location services
GOOGLE_MAPS_API_KEY=your_api_key_here
```

### Routes
```php
// Wizard route
Route::get('/it/tests/{slug}', [TestController::class, 'show'])->name('tests.view');
```

## Development Guidelines

### 1. Step Management
- Use `getCurrentStepIndex()` for step detection
- Implement proper step validation
- Handle step transitions smoothly
- Preserve form data between steps

### 2. Form Schema
- Use `TicketForm` methods for consistency
- Follow step-based schema approach
- Implement proper validation rules
- Use Italian translations for labels

### 3. View Development
- Use Design Comuni CSS classes
- Implement responsive design
- Add proper accessibility features
- Include loading states and error handling

### 4. Validation
```php
public function nextStep(): void
{
    $rules = match ($this->currentStep) {
        1 => ['data.privacyAccepted' => 'accepted'],
        2 => [
            'data.name' => 'required|string|max:255',
            'data.type' => 'required',
            'data.priority' => 'required',
            'data.content' => 'required|string',
        ],
        default => [],
    };

    $this->validate($rules);
}
```

## Integration Patterns

### With Filament
```php
// Form component integration
return $form
    ->schema($this->getCurrentStepSchema())
    ->statePath('data');
```

### With Design Comuni
```blade
<!-- Bootstrap Italia components -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">{{ __('fixcity::segnalazione.step.' . $currentStep . '.title') }}</h5>
    </div>
    <div class="card-body">
        {{ $this->form }}
    </div>
</div>
```

### With Localization
```php
// Italian translations
__('fixcity::segnalazione.wizard_a11y.skip_to_main.label')
__('fixcity::segnalazione.fields.required_note.label')
```

## Performance Considerations

### 1. Lazy Loading
- Load step components only when needed
- Implement conditional rendering
- Use proper state management

### 2. Validation
- Client-side validation for better UX
- Server-side validation for security
- Debounced validation for performance

### 3. Asset Loading
- Minimize CSS/JS bundle size
- Use proper caching strategies
- Implement lazy loading for heavy components

## Testing

### Unit Tests
```php
// Test step navigation
public function test_step_navigation()
{
    $widget = new CreateTicketWizardWidget();
    $widget->mount([]);
    
    $this->assertEquals(1, $widget->getCurrentStepIndex());
    
    $widget->nextStep();
    $this->assertEquals(1, $widget->getCurrentStepIndex());
}
```

### Integration Tests
```php
// Test form submission
public function test_form_submission()
{
    $response = $this->post('/it/tests/segnalazione-crea', [
        'data' => [
            'name' => 'Test User',
            'type' => 'complaint',
            'priority' => 'high',
            'content' => 'Test complaint',
        ],
    ]);
    
    $response->assertRedirect('/it/tests/segnalazione-04-conferma');
}
```

## Troubleshooting

### Common Issues
1. **Form Not Showing**: Check widget inheritance and view paths
2. **Step Navigation**: Verify step management logic
3. **Validation Errors**: Check validation rules and messages
4. **Translation Issues**: Verify translation file syntax

### Debug Tips
- Use Laravel DebugBar for debugging
- Check Livewire components for form state
- Verify routes and middleware
- Test in different environments

## Future Enhancements

### Planned Features
1. **Progress Indicator**: Visual progress bar for wizard steps
2. **Save Draft**: Save progress and return later
3. **Conditional Steps**: Dynamic step based on user input
4. **File Upload**: Attach files to segnalazioni

### Technical Improvements
1. **TypeScript**: Convert JavaScript to TypeScript
2. **Testing**: Comprehensive test coverage
3. **Performance**: Optimizations for large forms
4. **Accessibility**: Enhanced accessibility features

---

*Last Updated: May 2026*  
*Version: 1.0.0*
# Wizard Architecture Documentation

## Critical Architecture Pattern: URL State Persistence

### The Problem
Multi-step wizards must maintain their state across browser interactions, including:
- Navigation (back/forward buttons)
- Bookmarking specific steps
- URL sharing
- Browser refresh

### The Solution: `persistStepInQueryString()`

In `XotBaseWizardWidget::getWizardComponent()`:

```php
public function getWizardComponent(): Wizard
{
    $wizard = $this->getParentWizardComponent();
    
    // CRITICAL: Persist wizard state in URL
    $wizard = $wizard->persistStepInQueryString();
    
    if (! inAdmin()) {
        $wizard = $wizard->view('pub_theme::components.wizard');
    }

    return $wizard;
}
```

### Why This is Essential

#### 1. User Experience (UX)
- **Consistent Navigation**: Users can navigate between steps
- **Bookmarkability**: Save specific steps for later reference
- **Shareability**: Share links to specific wizard steps
- **State Recovery**: Refresh browser without losing progress

#### 2. Technical Benefits
- **SEO Friendly**: Individual steps are crawlable
- **Analytics**: Track step-specific user behavior
- **Debugging**: URL shows current step for debugging
- **Integration**: Other systems can link to specific steps

#### 3. Browser Integration
- **History Management**: Back/forward buttons work correctly
- **State Synchronization**: Multiple tabs can share state
- **Mobile Compatibility**: Touch gesture navigation works

### Implementation Rules

#### Rule 1: Always Use `persistStepInQueryString()`
```php
// ✅ CORRECT
$wizard = $wizard->persistStepInQueryString();

// ❌ WRONG - Missing URL persistence
$wizard = $this->getParentWizardComponent();
```

#### Rule 2: Implement in Base Widget
- **Never override this method** in child widgets
- **Always extend** `XotBaseWizardWidget` for wizards
- **Consistent implementation** across all wizards

#### Rule 3: Handle Different Environments
```php
public function getWizardComponent(): Wizard
{
    $wizard = $this->getParentWizardComponent();
    $wizard = $wizard->persistStepInQueryString();
    
    // Different views for admin vs public
    if (! inAdmin()) {
        $wizard = $wizard->view('pub_theme::components.wizard');
    }
    
    return $wizard;
}
```

### Step Management

#### Current Step Detection
```php
public function getStartStep(): int
{
    // Check URL parameter for step persistence
    $stepParam = request('step');
    if ($stepParam) {
        $steps = $this->getSteps();
        $stepKeys = array_keys($steps);
        $index = array_search($stepParam, $stepKeys, true);
        if ($index !== false) {
            return $index + 1; // Convert to 1-based index
        }
    }
    
    return $this->wizardStartStep ?? 1;
}
```

#### URL Patterns
```
// Default step
/it/tests/segnalazione-crea

// Specific step
/it/tests/segnalazione-crea?step=privacy
/it/tests/segnalazione-crea?step=data
```

### Integration Patterns

#### With Design Comuni
```blade
<!-- Wizard component uses Design Comuni styling -->
@push('styles')
    <link href="{{ Vite::asset('css/wizard.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ Vite::asset('js/wizard.js') }}"></script>
@endpush
```

#### With Laravel Routes
```php
// Route supports step parameter
Route::get('/it/tests/{slug}', [TestController::class, 'show'])
    ->name('tests.view')
    ->where(['slug' => '[a-zA-Z0-9-]+']);
```

### Troubleshooting

#### Common Issues
1. **State Not Persisting**: Missing `persistStepInQueryString()`
2. **Step Not Found**: Incorrect step key mapping
3. **URL Encoding**: Special characters in step names
4. **Navigation Issues**: Missing view configuration

#### Debugging Tips
- Check browser URL for step parameter
- Verify wizard component view rendering
- Test back/forward navigation
- Validate step key mapping

### Performance Considerations

#### URL Handling
- **Efficient Parsing**: Minimal URL parameter parsing
- **Caching**: URL state doesn't require additional caching
- **Memory**: No additional memory overhead

#### Client-Side
- **JavaScript**: Minimal JS needed for URL handling
- **CSS**: Optimized for wizard navigation
- **Network**: No additional network requests for state

---

*Critical Architecture Pattern - Must be implemented in all wizard widgets*  
*Last Updated: May 2026*  
*Version: 1.0.0*
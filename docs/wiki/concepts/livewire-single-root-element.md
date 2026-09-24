# Livewire Single Root Element Rule

## Problem
Livewire throws `MultipleRootElementsDetectedException` when a component's Blade template has:

- Multiple root-level DOM elements
- Comments outside the wrapper div (treated as root elements)
- Improperly closed wrapper causing extra content outside

## Example Error
```blade
<!-- ticket-create-wizard.blade.php -->
<div>  <!-- Root wrapper start -->
    <!-- step content -->
</div>  <!-- Line 174: wrapper closes -->

{{-- This comment is OUTSIDE the wrapper! --}}  <!-- Root element #2! -->
<div class="modal fade" id="modal-termini">...</div>  <!-- Root element #3! -->
</div>  <!-- Extra closing tag! -->

{{-- Inline wizard script note --}}  <!-- Another root element! -->
```

## Solution

### Fix Pattern: Move All Content Inside Single Wrapper

```blade
<div>
    <!-- ALL content must be inside this single root -->
    @foreach($steps as $step)
        <div class="step-{{ $step }}">
            <!-- step content -->
        </div>
    @endforeach
    
    <!-- Modals go INSIDE the wrapper -->
    <div class="modal fade" id="modal-termini">...</div>
    
    <!-- Comments are fine INSIDE -->
    {{-- Inline wizard script removed. See docs/wiki --}}
</div>
```

### Blade Syntax Rules

1. **One opening `<div>`** that wraps everything
2. **One closing `</div>`** at the very end
3. **No content after closing tag** (not even comments)
4. **Modals/dialogs** must be inside the wrapper
5. **Scripts** can be inline or in `<script>` tags inside wrapper

## Best Practices

### Template Structure Checklist
- [ ] Exactly one root element (not counting `@php/@endphp`)
- [ ] All components wrapped in single parent
- [ ] Modals are children of wrapper, not siblings
- [ ] No Blade comments after closing wrapper tag
- [ ] Test with Livewire: `$this->dispatch('some-event')`

### Testing
- Verify step transitions work
- Check modal opens/closes correctly
- Test validation messages appear properly
- Ensure JavaScript interactions work

## Common Pitfalls

### Pitfall 1: Comments Outside Wrapper
```blade
<div>
    @livewire('component')
</div>
{{-- Comment OUTSIDE --}}  <!-- BREAKS -->
```

### Pitfall 2: Modal Outside Wrapper
```blade
<div>
    <div wire:loading>Loading...</div>
</div>
<!-- Modal OUTSIDE wrapper --><div class="modal">...</div>  <!-- BREAKS -->
```

### Pitfall 3: Unclosed Tags
```blade
<div>
    <div wire:model="data">
        <!-- Missing closing div -->
```

## Related Concepts

- **Livewire component structure**: Single root element requirement
- **Blade template organization**: Keep logic in component, markup in template
- **Design Comuni compliance**: Global CSS for components, not page-specific

## Files Fixed (Example)

- `laravel/Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`

## Further Reading

- Livewire docs: https://laravel-livewire.com/docs/2.x/rendering-components
- Filament docs: https://filamentphp.com/docs/2.x/livewire-components
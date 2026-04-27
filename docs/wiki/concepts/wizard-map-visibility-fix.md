# Wizard Map Visibility Fix

## Problem
When transitioning to the map step in the ticket creation wizard, the map appears gray/empty because:

1. Leaflet initializes when the container is hidden (display: none)
2. The container has 0x0 dimensions during initialization
3. ResizeObserver and IntersectionObserver don't detect this type of visibility change

## Solution

### 1. IntersectionObserver in MapPickerLit.js

```javascript
// In connectedCallback()
this._observer = new IntersectionObserver(
    entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting && this._map) {
                setTimeout(() => this._map.invalidateSize(), 100);
            }
        });
    },
    { threshold: 0.1 }
);
const container = this.querySelector('.map-container');
if (container) {
    this._observer.observe(container);
}

// In disconnectedCallback()
this._observer?.disconnect();
```

### 2. MutationObserver Fallback

For cases where CSS classes change without DOM mutations:

```javascript
this._mutationObserver = new MutationObserver(() => {
    if (this.offsetParent !== null) {
        setTimeout(() => this._refreshMapSize(), 150);
    }
});
let parent = this.parentElement;
for (let i = 0; i < 6 && parent; i++) {
    this._mutationObserver.observe(parent, {
        attributes: true,
        attributeFilter: ['class', 'style', 'hidden']
    });
    parent = parent.parentElement;
}
```

### 3. Build Workflow

After modifying JS files:
```bash
cd laravel/Themes/Sixteen
npm run build
npm run copy
```

## Best Practices

- Always use IntersectionObserver for visibility-based UI updates
- Add timeout for invalidateSize to allow DOM updates to complete
- Disconnect observers in disconnectedCallback to prevent memory leaks
- Monitor both intersection and attribute changes for robustness

## False Friends

- ❌ `setTimeout(map.invalidateSize, 0)` - too fast, DOM not ready
- ✅ `setTimeout(map.invalidateSize, 100)` - allows DOM to update

## Related Files

- `laravel/Modules/Geo/resources/js/components/map-picker-lit.js`
- `laravel/Themes/Sixteen/resources/css/app.css`
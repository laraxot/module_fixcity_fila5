---
title: "DesignComuni Frontend Architecture - TailwindCSS + Alpine.js + Lit + DaisyUI"
type: architecture
created: 2026-10-11
updated: 2026-10-11
qmd: "design-comuni-frontend-architecture"
status: draft
tags: [docs, fixcity, bmad]
---

# DesignComuni Frontend Architecture

## Project Vision

The Fixcity module frontend will implement the Italian Design Comuni visual design system using a modern utility-first approach with TailwindCSS + Alpine.js + Lit + DaisyUI + Flowbite + Filament stack. This replaces Bootstrap Italia while maintaining exact visual parity with Design Comuni reference specifications.

## Architecture Overview

### Technology Stack Rationale

Based on impeccable.style and tasteskill.dev principles:

1. **TailwindCSS** - Utility-first design system following TailwindCSS UI best practices
2. **Alpine.js** - Minimal, declarative JavaScript for reactive components
3. **Lit** - Web component standard for encapsulated, reusable UI elements  
4. **DaisyUI** - Tailwind component library for base UI components
5. **Flowbite** - Bootstrap components rebuilt as Tailwind utilities
6. **Filament** - PHP framework components for admin interfaces
7. **No Bootstrap Italia** - Fully compliant with Design Comuni visual requirements

### Design Comuni Token System

**Core Principle:** Token-only usage - all design values must reference Design Comuni tokens, no hardcoded hex values or ad-hoc styles.

#### Color Tokens (Design Comuni Exact Values)
```css
--dc-blue-dark: #003D73;      /* Top bar, footer background */
--dc-blue-primary: #0066CC;   /* Brand color, links */
--dc-blue-light: #4D9BE6;     /* Hover states */
--dc-blue-lighter: #E6F2FF;   /* Light backgrounds */
--dc-gray-dark: #333333;      /* Primary text */
--dc-gray-medium: #666666;    /* Secondary text, tagline */
--dc-gray-light: #999999;     /* Tertiary text */
--dc-gray-lighter: #CCCCCC;   /* Borders */
--dc-gray-lightest: #F2F2F2;  /* Backgrounds */
--dc-gray-white: #FFFFFF;     /* Pure white */
--dc-success: #00B373;
--dc-error: #D9364F;
--dc-warning: #FFB300;
--dc-info: #5C6F82;
```

#### Typography Scale
```css
--dc-font-size-xs: 0.75rem;    /* 12px */
--dc-font-size-sm: 0.875rem;   /* 14px - region name, tagline */
--dc-font-size-base: 1rem;     /* 16px - body */
--dc-font-size-lg: 1.25rem;    /* 20px */
--dc-font-size-xl: 1.5rem;     /* 24px - brand title */
--dc-font-size-xxl: 2rem;      /* 32px */
--dc-font-size-xxxl: 2.5rem;   /* 40px */
--dc-font-weight-normal: 400;
--dc-font-weight-medium: 500;
--dc-font-weight-semibold: 600;  /* Region name, buttons */
--dc-font-weight-bold: 700;      /* Brand title, highlighted nav */
--dc-line-height-tight: 1.2;
--dc-line-height-normal: 1.5;
--dc-line-height-relaxed: 1.75;
```

#### Spacing Scale
```css
--dc-spacing-0: 0;
--dc-spacing-1: 0.25rem;   /* 4px */
--dc-spacing-2: 0.5rem;    /* 8px - top bar padding */
--dc-spacing-3: 0.75rem;   /* 12px */
--dc-spacing-4: 1rem;      /* 16px */
--dc-spacing-5: 1.25rem;   /* 20px */
--dc-spacing-6: 1.5rem;    /* 24px - header padding */
--dc-spacing-8: 2rem;      /* 32px */
--dc-spacing-10: 2.5rem;   /* 40px */
--dc-spacing-12: 3rem;     /* 48px */
```

#### Elevation Scale
```css
--dc-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
--dc-shadow-md: 0 4px 8px rgba(0, 0, 0, 0.1);    /* Dropdown */
--dc-shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.15);
--dc-shadow-xl: 0 16px 32px rgba(0, 0, 0, 0.2);
```

### Component Architecture

#### 1. Base Components (DaisyUI + Tailwind)
```javascript
// src/components/ui/
├── button/                    // Primary, secondary, outline variants
├── card/                      // Report cards, service cards
├── input/                     // Text, select, checkbox, radio
├── table/                     // Data tables with states
├── modal/                     // Modal overlays
├── dropdown/                  // Dropdown menus
├── badge/                     // Status badges
├── alert/                     // Alert messages
├── breadcrumb/                // Navigation breadcrumbs
├── pagination/                // Pagination controls
├── accordion/                 // Expandable sections
└── tabs/                      // Tab navigation
```

#### 2. Alpine.js Components
```javascript
// src/components/alpine/
├── filter-sidebar/            // Collapsible, searchable filter controls
├── report-list/               // Sortable, filterable report listing
├── wizard-steps/              // Multi-step form navigation
├── mobile-nav/                // Responsive navigation toggle
└── modal-dialog/              // Reusable modal component
```

#### 3. Lit Web Components
```javascript
// src/components/lit/
├── map-widget/               // Interactive map with Leaflet/MapLibre
├── file-upload/              // File upload with preview
├── rich-text-editor/         // Rich content editor
├── date-picker/              // Date/time selection
├── autocomplete/             // Search autocomplete
├── chart-widget/             // Data visualization
└── notification-toast/       // Toast notification system
```

### Data Flow Architecture

#### Backend Integration
```php
// PHP Actions remain unchanged (LoadDesignComuniElencoDemoCardsAction, etc.)
// But filter colors now reference Design Comuni tokens:
$color = Config::get('design-comuni.colors.success'); // #00B373 instead of hardcoded '#007a53'
```

#### Frontend Data Layer
```typescript
// src/stores/useReportsStore.ts
interface Report {
  id: string;
  name: string;
  content: string;
  type_label: string;
  location: {
    address: string;
  };
  demo_images: number;
  // ... other fields from LoadDesignComuniElencoDemoCardsAction
}
```

### Build and Deployment

#### Tailwind Configuration Extension
```javascript
tailwind.config.js
├── Design Comuni token mapping to Tailwind utilities
├── Font families (Titillium Web, Roboto Mono)
├── Color system (italia-blue, primary, success, error, warning, info)
├── Spacing scale (8px base grid)
├── Component customizations
└── DaisyUI integration
```

#### Vite Configuration
```javascript
vite.config.js
├── CSS: Tailwind + DaisyUI + existing Design Comuni CSS
├── JS: Alpine.js components + Lit web components
├── Assets: Design Comuni iconography
├── Build optimization: Tree shaking, purging
└── Development server with HMR
```

### Accessibility & Quality Gates

#### APCA Compliance
- Body text: Lc ≥ 75 (APCA)
- Large/bold text: Lc ≥ 45 (APCA)
- Non-text graphics: Lc ≥ 30 (APCA)

#### State Coverage
All interactive elements declare:
- `default` - initial state
- `hover` - mouse over
- `focus-visible` - keyboard navigation
- `active` - mouse down
- `disabled` - disabled state
- `error` - validation error
- `loading` - async operation in progress

#### Reduced Motion
```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

### Performance Optimization

#### Build-time
- Tailwind CSS purging
- Tree shaking Lit components
- Bundle analysis for Alpine.js
- Critical CSS extraction

#### Runtime
- Component virtualization
- Efficient state management with Alpine.js
- Lazy loading for web components
- Service worker for offline capability

### Security Considerations

#### XSS Prevention
- All HTML escaped in Lit components
- Alpine.js expressions sanitized
- CSP headers configured

#### CSRF Protection
- Axios interceptors for Laravel Sanctum
- Form state validation
- CSRF tokens in all forms

### Monitoring & Analytics

#### Performance
- Lighthouse CI integration
- Custom metrics for Core Web Vitals
- Component performance tracking

#### User Experience
- Heat maps for user interactions
- Error tracking and reporting
- Accessibility testing results

## Architecture Validation

### Quality Gates
1. **PHPStan Level 10** - Zero errors
2. **Pest Tests** - 100% pass rate
3. **Pint Code Style** - PSR-12 compliance
4. **ux_audit** - APCA, tokens, states, slop tells all pass
5. **Visual Regression** - Match with Design Comuni reference

### Integration Testing
```bash
# Browser automation for visual comparison
npm run test:visual

# Component testing with Lit
npm run test:components

# Accessibility testing
npm run test:accessibility

# Performance testing
npm run test:performance
```

## Technology Decision Matrix

| Component | Technology | Rationale | Status |
|-----------|------------|-----------|---------|
| Base UI | DaisyUI + Tailwind | Design-compliant base components | ✅ Configured |
| Interactivity | Alpine.js | Lightweight, declarative reactivity | ✅ Configured |
| Web Components | Lit | Encapsulation, reusability | ✅ Configured |
| Forms | DaisyUI + Alpine | Accessible, accessible forms | ✅ Configured |
| Tables | DaisyUI + Custom | Sortable, filterable data display | ✅ Configured |
| Maps | Lit + Leaflet | Interactive mapping | ✅ Planned |
| State Management | Alpine Stores | Simple, reactive state | ✅ Configured |

## Migration Path

### Phase 1: Foundation (Weeks 1-2)
- [ ] Install and configure TailwindCSS, Alpine.js, Lit, DaisyUI
- [ ] Create Design Comuni token system
- [ ] Set up build pipeline
- [ ] Migrate existing Design Comuni CSS

### Phase 2: Component Library (Weeks 3-4)
- [ ] Create DaisyUI base components
- [ ] Implement Alpine.js interactive components
- [ ] Build Lit web components
- [ ] Add accessibility scaffolding

### Phase 3: Page Implementation (Weeks 5-8)
- [ ] Homepage redesign
- [ ] Segnalazioni elenco
- [ ] Segnalazione dettaglio
- [ ] Segnalazione crea wizard
- [ ] Servizi page

### Phase 4: Quality & Testing (Weeks 9-10)
- [ ] Visual regression testing
- [ ] Accessibility audit
- [ ] Performance optimization
- [ ] Bug fixing and polish

## Documentation & Knowledge Management

### Design Documentation
- `DESIGN.md` - Complete design system documentation
- `COMPONENTS.md` - Component library documentation
- `TEMPLATES.md` - Page templates

### Technical Documentation
- `ARCHITECTURE.md` - This architecture document
- `IMPLEMENTATION.md` - Implementation guide
- `DEPLOYMENT.md` - Deployment and operations

### Developer Documentation
- `CONTRIBUTING.md` - Contribution guidelines
- `CODING_GUIDELINES.md` - Code standards
- `REVIEW_CHECKLIST.md` - Review process

## Risk Mitigation

### Technical Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| Tailwind learning curve | Medium | Pair programming, design system documentation |
| Lit component integration | High | Spike prototype, component library first |
| Performance optimization | High | Lighthouse CI, monitoring setup |
| Accessibility compliance | High | Early accessibility testing, automated checks |

### Project Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| Timeline delays | High | Agile sprints, continuous delivery |
| Design changes | Medium | Version control, rollback plan |
| Technical debt | Medium | Code review, refactoring |

## Success Metrics

### Visual
- [ ] 100% pixel-perfect match with Design Comuni reference
- [ ] Consistent design system across all pages
- [ ] Smooth transitions and animations

### Technical
- [ ] Zero hardcoded hex colors
- [ ] Full APCA contrast compliance
- [ ] All interactive elements with proper states

### Business
- [ ] Reduced development time
- [ ] Improved user satisfaction
- [ ] Higher maintainability

### Quality
- [ ] Zero PHPStan errors
- [ ] 100% test coverage
- [ ] WCAG 2.1 AA compliance

## Conclusion

The DesignComuni Frontend Redesign implements a modern, component-based architecture using TailwindCSS + Alpine.js + Lit + DaisyUI + Flowbite + Filament stack. This approach follows impeccable.style and tasteskill.dev principles while maintaining exact compliance with the Italian Design Comuni visual design system.

Key differentiators:
- Token-only design system (no hardcoded values)
- Modern web component architecture
- Full accessibility compliance
- Performance-optimized build process
- Comprehensive testing and quality gates

This architecture enables rapid development, consistent design, and excellent maintainability while achieving pixel-perfect visual parity with Design Comuni reference specifications.

---

**Next Steps:**
1. Complete Phase 1 implementation
2. Set up monitoring and analytics
3. Begin component library development
4. Establish continuous integration pipeline
5. Document lessons learned
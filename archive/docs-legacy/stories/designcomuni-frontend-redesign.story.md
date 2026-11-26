---
type: epic-story
title: "DesignComuni Frontend Redesign using TailwindCSS + Alpine.js + Lit + DaisyUI"
links: {github_issue: #55}
---

## Story Description
Redesign all frontend pages of the Fixcity module to comply with the Italian Design Comuni design system, replacing Bootstrap Italia with TailwindCSS + Alpine.js + Lit + DaisyUI stack. Focus on achieving pixel-perfect match with the Design Comuni reference implementation, particularly for the segnalazione (report) pages.

## Acceptance Criteria
- [ ] All frontend pages match Design Comuni reference visual specifications
- [ ] Use of Design Comuni tokens for colors, typography, spacing, etc.
- [ ] Implementation using TailwindCSS utility-first approach
- [ ] Interactive components built with Alpine.js
- [ ] Web components created with Lit for reusable UI elements
- [ ] DaisyUI used for base component styling where appropriate
- [ ] Zero usage of Bootstrap Italia
- [ ] Accessibility compliance (WCAG 2.1 AA)
- [ ] Responsive design following Design Comuni breakpoints
- [ ] Proper integration with existing Laravel/PHP backend
- [ ] All Design Comuni components implemented: header, footer, cards, forms, buttons, etc.
- [ ] Segnalazione elenco and dettaglio pages match reference exactly
- [ ] Services page matches Design Comuni servizi reference
- [ ] Homepage matches Design Comuni homepage reference

## Technical Requirements
- Install and configure TailwindCSS, Alpine.js, Lit, DaisyUI via npm
- Create Design Comuni token configuration for Tailwind
- Convert existing CSS to Tailwind utility classes
- Create Alpine.js components for interactive elements
- Create Lit web components for complex UI elements
- Ensure all color usage references Design Comuni tokens
- Maintain backward compatibility with existing backend endpoints
- Update build process to include new frontend assets
- Add proper purging of unused CSS in production
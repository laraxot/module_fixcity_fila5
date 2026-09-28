---
title: STORY-039 — Puppeteer + Playwright UI/UX Testing (BMAD)
status: in-progress
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/39
discussion: https://github.com/laraxot/fixcity_fila5/discussions/39
---

## Objective
Test the application UI/UX programmatically with puppeteer and playwright to identify visual/functional gaps, accessibility issues, and performance problems. All tests follow BMAD + Second Brain methodology.

## Test Strategy

### Step 1: Environment Setup
- Install puppeteer-playwright MCP server
- Configure test environment for `http://localhost:8001/it`
- Set up screenshot capture, video recording, and performance metrics collection

### Step 2: Critical User Paths (Per Actor)

#### Anonymous User
1. **Homepage** (`/`)
   - Header logo link to homepage
   - CTA "Fai una segnalazione" button
   - Categories overview cards
   - Recent tickets map preview

2. **Ticket Creation Wizard** (`segnalazione-crea`)
   - Step 1 (Luogo): Map functionality, search autocomplete
   - Step 2 (Disservizio): Category selection, file upload preview
   - Step 3 (Autore): Form validation, privacy consent
   - Progress indicator, navigation between steps
   - Responsive behavior (mobile/desktop)

3. **Ticket Tracking** (`segnalazione-traccia/{code}`)
   - Code input form
   - Ticket status display
   - Timeline activities
   - Responsive layout

4. **Public Tickets Browse** (`segnalazioni` / `segnalazioni/mappa`)
   - Filter functionality
   - List/tile views
   - Search

#### Authenticated Citizen
1. **My Tickets Dashboard** (`/my-tickets`)
   - Ticket list with status badges
   - Quick actions (view, rate, comment)
   - Empty state messaging
   - Pagination

2. **Profile** (`/profile`)
   - Editable fields
   - Save/cancel functionality
   - Preferences section

#### PA Operator
1. **Admin Dashboard** (`/admin/tickets`)
   - Ticket table with bulk actions
   - Filters and search
   - Quick status changes
   - Export functionality

2. **Ticket Detail** (`/tickets/{id}`)
   - Timeline with activities
   - Comments section
   - Activity log
   - Responsive behavior

### Step 3: Test Categories

#### Visual Parity
- Compare with Design Comuni (`segnalazione-02-dati.html`)
- Color schemes, typography, spacing
- Component alignment and layout
- Image quality and loading states

#### Functionality
- Form validation (required fields, formats)
- Navigation flows between pages
- Interactive elements (buttons, links, forms)
- Authentication flows

#### Accessibility
- Screen reader compatibility
- Keyboard navigation
- ARIA labels and roles
- Color contrast ratios
- Focus management

#### Performance
- Page load times (Lighthouse)
- Resource loading (images, scripts, styles)
- Memory usage
- Network conditions (3G, 4G simulation)

#### Cross-browser/Device
- Chrome, Firefox, Safari (iOS), Chrome (Android)
- Desktop (1200+px), Tablet (768-1199px), Mobile (≤767px)

### Step 4: Puppeteer + Playwright Tests
```javascript
// Example puppeteer test (node)
const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
  const browser = await puppeteer.launch({ headless: false });
  const page = await browser.newPage();
  
  // Test homepage
  await page.goto('http://localhost:8001/it');
  await page.waitForLoadState('networkidle');
  
  // Check hero CTA button
  const ctaButton = await page.$('a[href*="/segnalazione-crea"]');
  await ctaButton.click();
  await page.waitForURL('**segnalazione-crea**');
  
  // Take screenshots
  await page.screenshot({ path: 'screenshots/homepage.png', fullPage: true });
  
  // Test wizard steps
  await page.goto('http://localhost:8001/it/segnalazione-crea');
  await page.waitForSelector('.wizard-step-1');
  await page.screenshot({ path: 'screenshots/wizard-step1.png' });
  
  // Save test results
  await page.evaluate(() => {
    return window.performance.getEntriesByType('navigation').map(nav => ({
      name: nav.name,
      duration: nav.duration,
      redirectCount: nav.redirectCount,
      transferSize: nav.transferSize,
    }));
  });
  
  await browser.close();
})();
```

```javascript
// Example playwright test (Node.js with Test SDK)
import { test, expect } from '@playwright/test';

test.describe('Public Ticket Tracking', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('http://localhost:8001/it/segnalazione-traccia/demo-code');
    await page.waitForLoadState('networkidle');
  });

  test('should display ticket status correctly', async ({ page }) => {
    await expect(page.locator('[data-testid="ticket-status"]')).toContainText('Pending');
    await expect(page.locator('[data-testid="timeline"]')).toBeVisible();
  });

  test('should show error for invalid code', async ({ page }) => {
    await page.goto('http://localhost:8001/it/segnalazione-traccia/invalid-code-12345');
    await page.waitForSelector('[data-testid="error-message"]');
    await expect(page.locator('[data-testid="error-message"]')).toBeVisible();
  });
});
```

### Step 5: Reporting
- Generate visual regression reports (Backstop, Percy)
- Accessibility audit (axe-core)
- Performance scores (Lighthouse)
- Console error logs
- Network request analysis
- Mobile app test (PWA)

### Step 6: Quality Gates
```bash
# Run all tests
npx playwright test --reporter json
npx lighthouse http://localhost:8001/it --output html --output-path reports/lighthouse.html
npx axe-core --include='http://localhost:8001/it' --reporter json > reports/accessibility.json

# Compare with baseline
npx backstop test
```

### Step 7: Analysis & Fixes
- Prioritize issues by severity (Critical > High > Medium > Low)
- Map issues to BMAD stories
- Implement fixes with quality gates
- Re-run tests to verify

## Second Brain Integration

- `docs/bmad/stories/STORY-039-puppeteer-testing.md` (this story)
- Update `docs/user-journey-maps.md` with test results
- Document issues in `docs/wiki/log.md`
- Create automation for CI/CD pipeline

## Task Dependencies
1. ✅ Puppeteer-playwright setup complete
2. ⌛ Test script creation (Step 3-5)
3. ⌛ CI integration (Step 6)
4. ⌛ Analysis & documentation (Step 7)
5. ⌛ Action on critical issues (Story implementation)

## Immediate Actions (Next 48 hours)
1. Install puppeteer-playwright MCP server
2. Write basic home page test
3. Write tracking page test
4. Establish test baseline
5. Document any discovered issues

---

## Test Environment Configuration
```json
{
  "testEnvironment": "node",
  "puppeteerPath": "/usr/bin/puppeteer",
  "playwrightVersion": "1.40+",
  "browser": "chromium",
  "viewport": { "width": 1920, "height": 1080 },
  "deviceEmulation": {
    "mobile": false,
    "userAgent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
  },
  "coba": false,
  "reporter": "list",
  "logLevel": "info",
  "screenshot": "only-on-failure",
  "video": "retain-on-failure"
}
```

import { test, expect } from '@playwright/test';
import { requirePlaywrightCredentials } from './support/credentials.js';

const viewports = [
    { name: 'desktop', width: 1280, height: 900 },
    { name: 'mobile', width: 375, height: 812 },
    { name: 'tablet', width: 768, height: 1024 },
];

const OUTPUT_DIR = '/tmp/header-parity';

test.describe('Header Logged Parity', () => {

    test.beforeAll(async () => {
        const fs = await import('fs');
        if (!fs.existsSync(OUTPUT_DIR)) {
            fs.mkdirSync(OUTPUT_DIR, { recursive: true });
        }
    });

    for (const vp of viewports) {
        test(`guest state ${vp.name}`, async ({ page }) => {
            await page.setViewportSize({ width: vp.width, height: vp.height });
            await page.goto('/it/', { waitUntil: 'networkidle', timeout: 45000 });
            await page.waitForTimeout(3000);

            // Verify slim bar shows guest login CTA, NOT user dropdown
            const guestCta = page.locator('[data-element="personal-area-login"]');
            await expect(guestCta).toBeVisible({ timeout: 5000 });

            // Verify no user dropdown exists
            const userDropdown = page.locator('.it-user-wrapper');
            await expect(userDropdown).toHaveCount(0);

            // Verify language switcher exists
            const langSwitcher = page.locator('.it-header-slim-wrapper .nav-item.dropdown').first();
            await expect(langSwitcher).toBeVisible({ timeout: 5000 });

            await page.screenshot({ path: `${OUTPUT_DIR}/guest-${vp.name}.png`, fullPage: false });
        });
    }

    for (const vp of viewports) {
        test(`logged state ${vp.name}`, async ({ page }) => {
            const TEST_USER = requirePlaywrightCredentials();

            await page.setViewportSize({ width: vp.width, height: vp.height });

            // Login via UI
            await page.goto('/it/auth/login', { waitUntil: 'networkidle', timeout: 45000 });
            await page.waitForTimeout(2000);

            // Livewire form: fill login credentials
            const emailField = page.locator('input[wire\\:model="data.email"]').first();
            const passwordField = page.locator('input[wire\\:model="data.password"]').first();
            await emailField.fill(TEST_USER.email);
            await passwordField.fill(TEST_USER.password);

            // Submit the Livewire form
            await page.locator('button[type="submit"]').first().click();
            await page.waitForTimeout(5000);

            // Navigate to homepage
            await page.goto('/it/', { waitUntil: 'networkidle', timeout: 45000 });
            await page.waitForTimeout(3000);

            // Verify user dropdown is present
            const userDropdown = page.locator('.it-user-wrapper');
            await expect(userDropdown).toBeVisible({ timeout: 5000 });

            // Verify guest CTA is NOT present
            const guestCta = page.locator('[data-element="personal-area-login"]');
            await expect(guestCta).toHaveCount(0);

            const nameSpan = userDropdown.locator('#header-user-toggle > span.d-none.d-lg-block');
            if (vp.width >= 992) {
                await expect(nameSpan).toBeVisible({ timeout: 3000 });
            } else {
                await expect(nameSpan).toBeHidden();
            }

            await page.screenshot({ path: `${OUTPUT_DIR}/logged-${vp.name}.png`, fullPage: false });
        });
    }

    for (const vp of viewports) {
        test(`logged dropdown open ${vp.name}`, async ({ page }) => {
            const TEST_USER = requirePlaywrightCredentials();

            await page.setViewportSize({ width: vp.width, height: vp.height });

            // Login
            await page.goto('/it/auth/login', { waitUntil: 'networkidle', timeout: 45000 });
            await page.waitForTimeout(2000);
            const emailField = page.locator('input[wire\\:model="data.email"]').first();
            const passwordField = page.locator('input[wire\\:model="data.password"]').first();
            await emailField.fill(TEST_USER.email);
            await passwordField.fill(TEST_USER.password);
            await page.locator('button[type="submit"]').first().click();
            await page.waitForTimeout(5000);

            await page.goto('/it/', { waitUntil: 'networkidle', timeout: 45000 });
            await page.waitForTimeout(3000);

            const userToggle = page.locator('#header-user-toggle');
            await userToggle.click();
            await expect(userToggle).toHaveAttribute('aria-expanded', 'true', { timeout: 5000 });

            const dropdownMenu = page.locator('#header-user-menu');
            await expect(dropdownMenu).toHaveClass(/show/, { timeout: 5000 });

            if (vp.width < 992) {
                const nameSpan = page.locator('#header-user-toggle > span.d-none.d-lg-block');
                await expect(nameSpan).toBeHidden();
            }

            await expect(dropdownMenu.locator('text=I miei servizi')).toBeVisible();
            await expect(dropdownMenu.locator('text=Le mie pratiche')).toBeVisible();
            await expect(dropdownMenu.locator('text=Notifiche')).toBeVisible();
            await expect(dropdownMenu.locator('text=Impostazioni')).toBeVisible();
            await expect(dropdownMenu.locator('text=Esci')).toBeVisible();
            await expect(dropdownMenu.locator('span.divider')).toHaveCount(1);

            const menuBox = await dropdownMenu.boundingBox();
            const toggleBox = await userToggle.boundingBox();
            if (menuBox && toggleBox) {
                expect(menuBox.x + menuBox.width).toBeLessThanOrEqual(toggleBox.x + toggleBox.width + 2);
            }

            await page.screenshot({ path: `${OUTPUT_DIR}/logged-dropdown-${vp.name}.png`, fullPage: false });
        });
    }
});

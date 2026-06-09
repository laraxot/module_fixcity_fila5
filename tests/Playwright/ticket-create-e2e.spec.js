import { test, expect } from '@playwright/test';

test.describe('Segnalazione crea wizard e2e', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/it/tests/segnalazione-crea', { waitUntil: 'networkidle' });
        await expect(page.locator('.fi-sc-wizard')).toBeAttached({ timeout: 15000 });
    });

    test('loads wizard with step navigation visible', async ({ page }) => {
        await expect(page.getByText(/dati/i).first()).toBeVisible();
        await expect(page.getByText(/privacy/i).first()).toBeVisible();
    });

    test('can navigate to data step and fill fields', async ({ page }) => {
        const nameInput = page.locator('input[name="name"]');
        await expect(nameInput).toBeVisible({ timeout: 10000 });
        await nameInput.fill('Test segnalazione e2e');

        const typeSelect = page.locator('select[name="type"]');
        await expect(typeSelect).toBeVisible();
        await typeSelect.selectOption({ index: 1 });
    });

    test('privacy step has data-element attribute', async ({ page }) => {
        const steps = page.locator('.fi-sc-wizard-step');
        const stepCount = await steps.count();
        const lastStep = steps.nth(stepCount - 1);

        await lastStep.click();
        await page.waitForTimeout(500);

        const privacyCheckbox = page.locator('[data-element="privacy-consent"]');
        await expect(privacyCheckbox).toBeAttached({ timeout: 10000 });
    });

    test('summary step shows confirmation button', async ({ page }) => {
        const confirmBtn = page.getByRole('button', { name: /Conferma e invia/i }).first();
        await expect(confirmBtn).toBeVisible({ timeout: 10000 });
    });
});

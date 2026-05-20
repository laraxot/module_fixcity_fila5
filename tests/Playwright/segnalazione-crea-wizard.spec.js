import { test, expect } from '@playwright/test';

test.describe('Segnalazione crea wizard submit wiring', () => {
    test('page exposes livewire form with wire submit handler', async ({ page }) => {
        const response = await page.goto('/it/tests/segnalazione-crea', {
            waitUntil: 'domcontentloaded',
        });

        expect(response?.ok()).toBeTruthy();

        const form = page.locator('form[wire\\:submit="submit"]');
        await expect(form).toHaveCount(1, { timeout: 20000 });
    });

    test('theme submit button view is present on last wizard step markup', async ({ page }) => {
        await page.goto('/it/tests/segnalazione-crea?step=3', {
            waitUntil: 'domcontentloaded',
        });

        const submitButton = page.locator('button.steppers-btn-confirm[type="submit"]');
        await expect(submitButton.first()).toBeVisible({ timeout: 20000 });
    });
});

import { test, expect } from '@playwright/test';

test.describe('Segnalazione crea wizard submit wiring', () => {
    test('page exposes livewire form with wire submit handler', async ({ page }) => {
        const response = await page.goto('/it/tests/segnalazione-crea', {
            waitUntil: 'networkidle',
        });

        expect(response?.ok()).toBeTruthy();

        const forms = page.locator('form[wire\\:submit="submit"]');
        await expect(forms.first()).toBeAttached({ timeout: 20000 });
        expect(await forms.count()).toBeGreaterThanOrEqual(1);
    });

    test('theme submit button markup is rendered for wizard', async ({ page }) => {
        await page.goto('/it/tests/segnalazione-crea?step=3', {
            waitUntil: 'networkidle',
        });

        const submitButton = page.locator('button.steppers-btn-confirm[type="submit"]');
        await expect(submitButton.first()).toBeAttached({ timeout: 20000 });
    });
});

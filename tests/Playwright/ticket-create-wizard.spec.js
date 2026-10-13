import { test, expect } from '@playwright/test';

test.describe('Segnalazione crea access boundary', () => {
    test('guest is sent to the localized login before the wizard renders', async ({ page }) => {
        const response = await page.goto('/it/tickets/create', {
            waitUntil: 'networkidle',
        });

        await expect(page).toHaveURL(/\/it\/auth\/login/);
        await expect(page.locator('.fi-sc-wizard')).toHaveCount(0);
    });

    test('guest cannot reach the summary step through a crafted query string', async ({ page }) => {
        await page.goto('/it/tickets/create?step=form.summary%3A%3Adata%3A%3Awizard-step', {
            waitUntil: 'networkidle',
        });

        await expect(page).toHaveURL(/\/it\/auth\/login/);
        await expect(page.locator('.fi-sc-wizard')).toHaveCount(0);
    });
});

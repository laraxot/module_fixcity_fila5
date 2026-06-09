import { test, expect } from '@playwright/test';

test.describe('Segnalazione crea wizard submit wiring', () => {
    test('page exposes filament wizard alpine submit wired to Livewire save', async ({ page }) => {
        const response = await page.goto('/it/tests/segnalazione-crea', {
            waitUntil: 'networkidle',
        });

        expect(response?.ok()).toBeTruthy();

        const html = await page.content();

        expect(html).toContain('$wire.save()');
        expect(html).toContain('fi-sc-wizard');
    });

    test('summary step uses Filament Infolist markup (TextEntry rows) via canonical Filament step id', async ({ page }) => {
        const canonicalStepId = encodeURIComponent('form.summary::data::wizard-step');

        await page.goto(`/it/tests/segnalazione-crea?step=${canonicalStepId}`, {
            waitUntil: 'networkidle',
        });

        await expect(page.locator('.fi-sc-wizard')).toBeAttached();

        await expect.poll(async () => await page.locator('.fi-in-entry').count()).toBeGreaterThan(3);

        // Footer Filament wizard: pulsante conferma usa label dalla Resource / Lang auto-label (locale it).
        await expect(
            page.getByRole('button', { name: /Conferma e invia|Salva|Save/i }).first(),
        ).toBeVisible({ timeout: 25000 });
    });
});

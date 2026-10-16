import { test, expect } from '@playwright/test';
import { requirePlaywrightCredentials } from './support/credentials.js';

test('demo PA operator signs in and opens the Fixcity ticket queue', async ({ page }) => {
    const credentials = requirePlaywrightCredentials();
    const pageErrors = [];
    page.on('pageerror', (error) => pageErrors.push(error.message));

    await page.goto('/fixcity/admin/login', { waitUntil: 'domcontentloaded' });
    await page.locator('input[type="email"]').fill(credentials.email);
    await page.locator('input[autocomplete="current-password"]').fill(credentials.password);
    await page.getByRole('button', { name: 'Accedi' }).click();

    await expect(page).not.toHaveURL(/\/fixcity\/admin\/login/);
    expect(pageErrors).toEqual([]);

    for (const width of [1440, 768, 390]) {
        await page.setViewportSize({ width, height: 1000 });
        const queueResponse = await page.goto('/fixcity/admin/tickets', { waitUntil: 'networkidle' });

        expect(queueResponse?.status(), `operator queue HTTP at ${width}px`).toBe(200);
        expect(page.url()).toContain('/fixcity/admin/tickets');
        await expect(page.locator('.fi-page')).toBeVisible();
        await expect(page.locator('.fi-wi-stats-overview')).toHaveCount(3);
        await expect(page.getByText('Volumi segnalazioni', { exact: true })).toBeVisible();
        await expect(page.locator('body')).not.toContainText(/Internal Server Error|No hint path defined|Exception trace/i);
        await expect(page.locator('body')).not.toContainText(/filament::components\/loading-section\.label|xot::/i);
        expect(await page.evaluate(() => document.documentElement.scrollWidth), `operator queue overflow at ${width}px`).toBeLessThanOrEqual(width);

        if (width === 390 || width === 1440) {
            await page.screenshot({ path: `/tmp/fixcity-operator-queue-${width}.png`, fullPage: true });
        }
    }

    expect(pageErrors).toEqual([]);
});

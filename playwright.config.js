import { defineConfig } from '@playwright/test';

const baseURL = process.env.PLAYWRIGHT_BASE_URL ?? 'http://127.0.0.1:8000';

export default defineConfig({
    testDir: './tests/Playwright',
    use: {
        baseURL,
        headless: true,
        actionTimeout: 15000,
    },
    timeout: 90000,
    workers: 1,
});

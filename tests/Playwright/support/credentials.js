/**
 * Playwright credentials — never hardcode secrets in specs.
 * Set PLAYWRIGHT_TEST_EMAIL and PLAYWRIGHT_TEST_PASSWORD in env / .env.playwright.
 */
export function requirePlaywrightCredentials(): { email: string; password: string } {
    const email = process.env.PLAYWRIGHT_TEST_EMAIL;
    const password = process.env.PLAYWRIGHT_TEST_PASSWORD;

    if (!email || !password) {
        throw new Error(
            'PLAYWRIGHT_TEST_EMAIL and PLAYWRIGHT_TEST_PASSWORD must be set (no hardcoded secrets in specs).',
        );
    }

    return { email, password };
}

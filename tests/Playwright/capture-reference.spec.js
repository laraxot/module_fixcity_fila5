const { test } = require('@playwright/test');

const REF_URL = 'https://italia.github.io/design-comuni-pagine-statiche/sito/ticket-list.html';

const viewports = [
  { name: 'desktop', width: 1280, height: 900 },
  { name: 'mobile', width: 375, height: 812 },
  { name: 'tablet', width: 768, height: 1024 },
];

for (const vp of viewports) {
  test(`capture reference ${vp.name}`, async ({ page }) => {
    await page.setViewportSize({ width: vp.width, height: vp.height });
    await page.goto(REF_URL, { waitUntil: 'networkidle', timeout: 60000 });
    await page.waitForTimeout(3000);
    await page.screenshot({ path: `/tmp/ref-${vp.name}.png`, fullPage: true });
  });
}

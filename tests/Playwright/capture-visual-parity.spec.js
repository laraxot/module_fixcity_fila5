const { test } = require('@playwright/test');
const path = require('path');

const viewports = [
  { name: 'desktop', width: 1280, height: 900 },
  { name: 'mobile', width: 375, height: 812 },
  { name: 'tablet', width: 768, height: 1024 },
];

for (const vp of viewports) {
  test(`capture local ${vp.name}`, async ({ page }) => {
    await page.setViewportSize({ width: vp.width, height: vp.height });
    await page.goto('/it/', { waitUntil: 'networkidle', timeout: 45000 });
    await page.waitForTimeout(3000);
    await page.screenshot({ path: `/tmp/local-${vp.name}.png`, fullPage: true });
  });
}

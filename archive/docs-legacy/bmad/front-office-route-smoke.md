# Front-office route smoke findings

The browser audit found a real source-of-truth mismatch: the Sixteen theme had a reusable `components/pages/services.blade.php` view and `FrontofficeRoutes::servicesIndex()`, but no `services.index` CMS page in the active module content set. `/it/services` therefore returned the CMS 404 page. The fix is `config/database/content/pages/services.index.json`, owned by Fixcity because it defines the public application page.

The login page had a second compatibility failure in persisted CMS blocks: older data could reference `pub_theme::components.pages.auth.login`, while the canonical reusable view is `auth-login.blade.php`. The small compatibility view at `Themes/Sixteen/resources/views/components/pages/auth/login.blade.php` preserves old data while the canonical caller remains unchanged.

Verification must run against the deployed host at 375, 768 and 1440 px, and must record status, title, console errors, failed requests and internal links. Playwright screenshots belong under the theme's docs screenshots area; generated assets are evidence, not application source.

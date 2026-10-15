import { test as base, expect } from '@playwright/test';
import { readFileSync } from 'node:fs';
import { requirePlaywrightCredentials } from './support/credentials.js';

/*
 * DCP-146: passo "Dati di segnalazione" del wizard su ORIGINE HTTP NON SICURA (window.isSecureContext === false).
 *
 * Esecuzione (baseURL = IP di rete locale o nome host NON localhost/127.0.0.1, che sono contesti sicuri):
 *   PLAYWRIGHT_BASE_URL=http://192.168.1.40:8000 \
 *   PLAYWRIGHT_TEST_EMAIL=... PLAYWRIGHT_TEST_PASSWORD=... \
 *   npx playwright test -c laravel/Modules/Fixcity/playwright.config.js dcp146
 * In alternativa a email e password: DCP146_STORAGE_STATE=<file json di storageState di una sessione gia' autenticata>.
 * Opzionale: DCP146_DEFAULT_VIEW="lat,lng,zoom" (default 42.5,12.5,6: panoramica d'Italia, vedi la story DCP-146).
 * Nessuna credenziale nel repo. I test NON inviano mai la segnalazione: si fermano al riepilogo.
 * Nominatim e' simulato con page.route (nessuna dipendenza dalla rete esterna, risultati deterministici).
 */

const LOCALES = ['it', 'en', 'de', 'es'];
const VIEWPORTS = [375, 768, 1440];
/** WCAG 2.5.8 (AA) chiede 24 px; 44 px e' la soglia delle linee guida touch: la chiediamo dove il puntatore e' grossolano (telefono e tablet). */
const TARGET_MIN_PX_COARSE = 44;
const TARGET_MIN_PX_FINE = 24;
const [DEFAULT_LAT, DEFAULT_LNG, DEFAULT_ZOOM] = (process.env.DCP146_DEFAULT_VIEW ?? '42.5,12.5,6').split(',').map(Number);

/** Testo che nomina HTTPS o la connessione sicura: vietato in tutte le lingue. */
const FORBIDDEN_TEXT = /https|connessione sicura|secure connection|sichere verbindung|conexi[oó]n segura/i;
/** Chiavi di traduzione grezze: `ns::file.chiave`, `ticket_form.fields...`, `coordinate-picker.xxx`. */
const RAW_KEY = /\b[a-z][\w-]*::[\w.-]+|\b(?:ticket_form|ticket_wizard|ticket_review|coordinate-picker|fixcity\.ticket)\.[\w.-]+/i;
const IP_SERVICES = /geojs|ipapi|ip-api|ipinfo|ipwho|ipify|ipgeolocation|freegeoip|ifconfig\.|myip/i;
const TILE_SERVERS = /tile\.openstreetmap\.org|arcgisonline\.com|cartocdn\.com|opentopomap\.org|tile\.thunderforest|stadiamaps/i;
const PIXEL = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGP4z8DwHwAFAAH/q842iQAAAABJRU5ErkJggg==', 'base64');
const LOCATE_BUTTON = {
    it: /Usa la mia posizione/i,
    en: /Use my location/i,
    de: /Meinen Standort verwenden/i,
    es: /Usar mi ubicaci[oó]n/i,
};

const REVERSE = { display_name: 'Via Prova 1, 00100 Roma RM, Italia', address: { road: 'Via Prova', house_number: '1', postcode: '00100', city: 'Roma', country: 'Italia' } };
const SEARCH = [{ lat: '45.4642', lon: '9.1900', display_name: 'Duomo di Milano, Milano, Italia', address: { road: 'Piazza del Duomo', city: 'Milano', country: 'Italia' } }];

const test = base.extend({
    /** Sessione autenticata, creata una volta per worker (il login ha un limite di tentativi). */
    session: [async ({ browser }, use) => {
        if (process.env.DCP146_STORAGE_STATE) {
            await use(JSON.parse(readFileSync(process.env.DCP146_STORAGE_STATE, 'utf8')));
            return;
        }
        const { email, password } = requirePlaywrightCredentials();
        const context = await browser.newContext();
        const page = await context.newPage();
        await page.goto('/it/auth/login', { waitUntil: 'networkidle' });
        await page.fill('input[type=email]', email);
        await page.fill('input[type=password]', password);
        await page.click('button[type=submit]');
        await page.waitForURL((url) => !url.pathname.endsWith('/auth/login'), { timeout: 20000 });
        const state = await context.storageState();
        await context.close();
        await use(state);
    }, { scope: 'worker' }],

    /** Apre il passo "dati" nella lingua e nella larghezza richieste, con Nominatim simulato. */
    openStep: async ({ browser, session, baseURL }, use) => {
        const contexts = [];
        await use(async (locale = 'it', width = 1440, { touch = false } = {}) => {
            const context = await browser.newContext({ storageState: session, viewport: { width, height: 900 }, timezoneId: 'Europe/Rome', locale: `${locale}-${locale.toUpperCase()}`, hasTouch: touch });
            contexts.push(context);
            const page = await context.newPage();
            const external = [];
            page.on('request', (request) => { if (!request.url().startsWith(baseURL ?? '')) external.push(request.url()); });
            const json = (body) => (route) => route.fulfill({ status: 200, contentType: 'application/json', headers: { 'access-control-allow-origin': '*', 'access-control-allow-headers': '*' }, body: JSON.stringify(body) });
            await page.route(/nominatim\.openstreetmap\.org\/reverse/, json(REVERSE));
            await page.route(/nominatim\.openstreetmap\.org\/search/, json(SEARCH));
            // Servizi di IP geolocation: la richiesta viene registrata in `external` e poi bloccata, cosi' la centratura si prova senza di loro.
            await page.route(IP_SERVICES, (route) => route.abort());
            // Tile delle mappe: PNG 1x1 locale, cosi' la geometria si prova senza rete esterna.
            await page.route(TILE_SERVERS, (route) => route.fulfill({ status: 200, contentType: 'image/png', body: PIXEL }));
            await page.goto(`/${locale}/tickets/create`, { waitUntil: 'networkidle' });
            const stepId = await page.evaluate(() => document.querySelectorAll('form.fi-sc-wizard-step')[1]?.id ?? '');
            expect(stepId, `slug del passo "dati" non trovato in /${locale}/tickets/create`).not.toBe('');
            await page.goto(`/${locale}/tickets/create?step=${encodeURIComponent(stepId)}`, { waitUntil: 'networkidle' });
            await page.waitForFunction(() => Boolean(document.querySelector('coordinate-picker-lit')?._map?.getZoom), null, { timeout: 15000 });
            return { page, external };
        });
        await Promise.all(contexts.map((context) => context.close()));
    },
});

/** Stato della mappa Leaflet del selettore (centro, zoom, segnaposto, valori mostrati). */
const mapState = (page) => page.evaluate(() => {
    const el = document.querySelector('coordinate-picker-lit');
    const map = el?._map;
    if (!map) return null;
    const root = el.shadowRoot ?? el;
    const center = map.getCenter();
    return {
        lat: center.lat,
        lng: center.lng,
        zoom: map.getZoom(),
        pins: root.querySelectorAll('.leaflet-marker-pane > *, .leaflet-overlay-pane path').length,
        readout: (document.querySelector('.geo-coordinate-readout')?.textContent ?? '').replace(/\s+/g, ' ').trim(),
        status: [...root.querySelectorAll('[role=status], [aria-live]')].map((n) => n.textContent.trim()).filter(Boolean).join(' | '),
    };
});

/** Testo visibile, aria-label, title e placeholder del widget del wizard (shadow DOM incluso), senza i nodi nascosti. */
const wizardText = (page) => page.evaluate(() => {
    const out = [];
    const walk = (node) => {
        for (const child of node.childNodes) {
            if (child.nodeType === Node.TEXT_NODE) {
                if (child.parentElement?.checkVisibility?.() !== false && child.data.trim()) out.push(child.data.trim());
            } else if (child.nodeType === Node.ELEMENT_NODE) {
                if (!['SCRIPT', 'STYLE'].includes(child.tagName) && child.checkVisibility?.() !== false) {
                    for (const attribute of ['aria-label', 'title', 'placeholder', 'alt']) {
                        if (child.getAttribute(attribute)) out.push(child.getAttribute(attribute));
                    }
                    if (child.shadowRoot) walk(child.shadowRoot);
                }
                walk(child);
            }
        }
    };
    walk(document.querySelector('.cmp-wizard-widget') ?? document.body);
    return out;
});

/** Clic nella mappa a una frazione del riquadro; restituisce lat/lng attese dal centro del clic. */
async function clickMap(page, fx = 0.6, fy = 0.4) {
    const pane = page.locator('coordinate-picker-lit .leaflet-container').first();
    // La mappa sta sotto la piega (y 769-1167 con viewport alto 900): senza scorrere, mouse.click cadrebbe fuori dal viewport e non farebbe nulla.
    await pane.scrollIntoViewIfNeeded();
    const box = await pane.boundingBox();
    expect(box, 'riquadro della mappa non trovato').not.toBeNull();
    const expected = await page.evaluate(({ x, y }) => {
        const point = document.querySelector('coordinate-picker-lit')._map.containerPointToLatLng([x, y]);
        return { lat: point.lat, lng: point.lng };
    }, { x: box.width * fx, y: box.height * fy });
    await page.mouse.click(box.x + box.width * fx, box.y + box.height * fy);
    return expected;
}

/**
 * Tocca la mappa finche' compare il pin (al massimo 3 tocchi): un tocco a zoom basso puo' limitarsi a ingrandire sul punto toccato
 * (guardia DCP-127), purche' il tocco successivo sullo stesso punto lo piazzi. Restituisce lat/lng dell'ultimo tocco e quanti ne sono serviti.
 */
async function placePin(page) {
    let expected = null;
    for (let taps = 1; taps <= 3; taps += 1) {
        expected = await clickMap(page, taps === 1 ? 0.6 : 0.5, taps === 1 ? 0.4 : 0.5);
        await page.waitForTimeout(1200);
        if ((await mapState(page)).pins > 0) return { expected, taps };
    }
    throw new Error(`nessun pin dopo 3 tocchi sulla mappa. Stato: ${JSON.stringify(await mapState(page))}`);
}

test.describe('DCP-146 passo dati su origine HTTP non sicura', () => {
    test('la pagina e la mappa girano davvero su un contesto NON sicuro', async ({ openStep }) => {
        const { page } = await openStep('it');
        const secure = await page.evaluate(() => window.isSecureContext);
        expect(secure, 'baseURL e\' un contesto sicuro (localhost/127.0.0.1/HTTPS): usare PLAYWRIGHT_BASE_URL=http://<IP di rete>:8000').toBe(false);
    });

    test('la mappa e\' disegnata: pannelli Leaflet in posizione assoluta e tile dentro il riquadro (misura la geometria, non il numero di tile)', async ({ openStep }) => {
        const { page } = await openStep('it');
        await page.waitForTimeout(2500);
        const geometry = await page.evaluate(() => {
            const container = document.querySelector('coordinate-picker-lit .leaflet-container');
            const box = container.getBoundingClientRect();
            const tiles = [...container.querySelectorAll('img.leaflet-tile')].map((tile) => tile.getBoundingClientRect());
            const inside = tiles.filter((r) => r.right > box.left && r.left < box.right && r.bottom > box.top && r.top < box.bottom);

            return { panePosition: getComputedStyle(container.querySelector('.leaflet-pane')).position, tiles: tiles.length, inside: inside.length, box: `${Math.round(box.width)}x${Math.round(box.height)}` };
        });
        expect(geometry.panePosition, JSON.stringify(geometry)).toBe('absolute');
        expect(geometry.tiles, JSON.stringify(geometry)).toBeGreaterThanOrEqual(4);
        expect(geometry.inside / geometry.tiles, `tile fuori dal riquadro: ${JSON.stringify(geometry)}`).toBeGreaterThanOrEqual(0.8);
    });

    test('la mappa parte centrata sulla vista di default o sul punto salvato, mai sul mondo intero (zoom 3)', async ({ openStep }) => {
        const { page } = await openStep('it');
        await expect.poll(async () => (await mapState(page))?.zoom ?? 0, { timeout: 8000, message: 'zoom iniziale della mappa' }).toBeGreaterThanOrEqual(5);
        const state = await mapState(page);
        expect(Math.abs(state.lat - DEFAULT_LAT), `centro ${state.lat},${state.lng} lontano dalla vista di default ${DEFAULT_LAT},${DEFAULT_LNG}`).toBeLessThanOrEqual(8);
        expect(Math.abs(state.lng - DEFAULT_LNG), `centro ${state.lat},${state.lng} lontano dalla vista di default ${DEFAULT_LAT},${DEFAULT_LNG}`).toBeLessThanOrEqual(10);
        expect(state.zoom).toBeLessThanOrEqual(Math.max(DEFAULT_ZOOM, 12));
    });

    test('il clic sulla mappa piazza il pin e riempie coordinate e indirizzo', async ({ openStep }) => {
        const { page } = await openStep('it');
        await expect.poll(async () => (await mapState(page))?.zoom ?? 0, { timeout: 8000 }).toBeGreaterThanOrEqual(5);
        const { expected, taps } = await placePin(page);
        test.info().annotations.push({ type: 'tocchi per piazzare il pin', description: String(taps) });
        const after = await mapState(page);
        const numbers = (after.readout.match(/-?\d+\.\d+/g) ?? []).map(Number);
        expect(numbers.length, `coordinate non mostrate: "${after.readout}"`).toBeGreaterThanOrEqual(2);
        expect(Math.abs(numbers[0] - expected.lat)).toBeLessThan(0.02);
        expect(Math.abs(numbers[1] - expected.lng)).toBeLessThan(0.02);
        await expect(page.locator('input[wire\\:model="data.location.address"]')).toHaveValue(/Via Prova 1/, { timeout: 8000 });
    });

    test('AC1 (decisione DCP-146): sulla vista regionale il primo tocco ingrandisce sul punto toccato, il secondo piazza il pin e l\'invito resta finche\' il pin non c\'e\'', async ({ openStep }) => {
        const { page } = await openStep('it');
        await expect.poll(async () => (await mapState(page))?.zoom ?? 0, { timeout: 8000 }).toBeGreaterThanOrEqual(5);
        const invite = page.locator('coordinate-picker-lit').locator('.map-control-status');
        await expect(invite).toHaveText(/./);
        await clickMap(page);
        await expect.poll(async () => (await mapState(page))?.zoom ?? 0, { timeout: 5000 }).toBeGreaterThanOrEqual(10);
        expect((await mapState(page)).pins, 'il primo tocco sulla vista regionale non deve piazzare il pin (guardia DCP-127: evita punti a centinaia di km)').toBe(0);
        await expect(invite, 'l\'invito a toccare la mappa resta finche\' non c\'e\' un pin').toHaveText(/./);
        await clickMap(page);
        await expect.poll(async () => (await mapState(page)).pins, { timeout: 8000 }).toBeGreaterThan(0);
    });

    test('la ricerca indirizzo (anche da tastiera) centra la mappa e posiziona il pin', async ({ openStep }) => {
        const { page } = await openStep('it');
        const open = page.locator('coordinate-picker-lit').getByRole('button', { name: /^Cerca indirizzo$/i }).first();
        await open.focus();
        await page.keyboard.press('Enter');
        const input = page.locator('coordinate-picker-lit .map-picker-search-input');
        await expect(input, 'campo di ricerca non aperto con Invio sul pulsante').toBeVisible();
        await input.fill('Duomo Milano');
        await page.keyboard.press('Enter');
        await expect.poll(async () => Math.round((await mapState(page)).lat * 10) / 10, { timeout: 8000, message: 'la mappa non si e\' spostata su Milano' }).toBeCloseTo(45.5, 0);
        const after = await mapState(page);
        expect(after.zoom).toBeGreaterThanOrEqual(13);
        expect(after.pins).toBeGreaterThan(0);
        await expect(page.locator('input[wire\\:model="data.location.address"]')).toHaveValue(/Duomo di Milano/, { timeout: 8000 });
    });

    test('nessuna richiesta a servizi di geolocalizzazione per IP di terzi (decisione DCP-146, GDPR art. 6 e 44)', async ({ openStep }) => {
        const { page, external } = await openStep('it');
        await page.waitForTimeout(4000);
        await page.locator('coordinate-picker-lit').getByRole('button', { name: LOCATE_BUTTON.it }).first().click();
        await page.waitForTimeout(1500);
        const ipServices = external.filter((url) => IP_SERVICES.test(url));
        expect(ipServices, 'il browser ha inviato l\'IP a un servizio terzo').toEqual([]);
    });

    for (const locale of LOCALES) {
        test(`[${locale}] "Usa la mia posizione" non e' un pulsante morto e non nomina HTTPS`, async ({ openStep }) => {
            const { page } = await openStep(locale);
            await page.waitForTimeout(1500);
            const button = page.locator('coordinate-picker-lit').getByRole('button', { name: LOCATE_BUTTON[locale] }).first();
            await expect(button, `pulsante posizione assente in ${locale}`).toBeVisible();
            const before = { state: await mapState(page), text: (await wizardText(page)).join('|') };
            await button.click();
            await page.waitForTimeout(3500);
            const after = { state: await mapState(page), text: (await wizardText(page)).join('|') };
            const viewMoved = before.state.lat !== after.state.lat || before.state.lng !== after.state.lng || before.state.zoom !== after.state.zoom;
            const feedback = viewMoved || before.text !== after.text || after.state.status !== '';
            expect(feedback, `nessuna reazione al clic: ${JSON.stringify(after.state)}`).toBe(true);
            expect((await wizardText(page)).filter((line) => FORBIDDEN_TEXT.test(line)), 'testo su HTTPS o connessione sicura').toEqual([]);
        });

        test(`[${locale}] nessun testo HTTPS ne' chiave di traduzione grezza nel passo`, async ({ openStep }) => {
            const { page } = await openStep(locale);
            await page.waitForTimeout(1500);
            const lines = await wizardText(page);
            expect(lines.filter((line) => FORBIDDEN_TEXT.test(line)), 'testo su HTTPS o connessione sicura').toEqual([]);
            expect(lines.filter((line) => RAW_KEY.test(line)), 'chiavi di traduzione grezze visibili').toEqual([]);
            expect(await page.evaluate(() => document.documentElement.lang), 'attributo lang di <html>').toMatch(new RegExp(`^${locale}`, 'i'));
        });

        test(`[${locale}] il passo si completa (punto, nome, tipo, dettagli) e porta al riepilogo senza inviare`, async ({ openStep }) => {
            const { page } = await openStep(locale);
            await expect.poll(async () => (await mapState(page))?.zoom ?? 0, { timeout: 8000 }).toBeGreaterThanOrEqual(5);
            await placePin(page);
            await expect(page.locator('input[wire\\:model="data.location.address"]')).toHaveValue(/Via Prova 1/, { timeout: 8000 });
            const name = `Prova DCP-146 ${locale}`;
            await page.locator('input[wire\\:model="data.name"]').fill(name);
            const nativeSelect = page.locator('select[id="form.type"]');
            if (await nativeSelect.count()) {
                await nativeSelect.selectOption({ index: 1 });
            } else {
                await page.locator('[id="form.type"]').click();
                await page.locator('li[role=option]:visible').first().click();
            }
            await page.locator('textarea[wire\\:model="data.content"]').fill('Descrizione di prova per il test automatico del passo dati.');
            await page.locator('.cmp-wizard-widget .steppers-btn-confirm:visible').click();
            const activeIndex = () => page.evaluate(() => [...document.querySelectorAll('form.fi-sc-wizard-step')].findIndex((form) => form.classList.contains('fi-active')));
            try {
                await expect.poll(activeIndex, { timeout: 12000 }).toBe(2);
            } catch (error) {
                const errors = await page.locator('[role=alert]:visible, .invalid-feedback:visible, .fi-fo-field-wrp-error-message:visible').allInnerTexts();
                throw new Error(`il wizard non e' passato al riepilogo (passo attivo ${await activeIndex()}; errori visibili: ${errors.join(' / ') || 'nessuno'})`, { cause: error });
            }
            const lines = await wizardText(page);
            expect(lines.join(' ')).toContain(name);
            expect(lines.filter((line) => RAW_KEY.test(line)), 'chiavi grezze nel riepilogo').toEqual([]);
            expect(lines.filter((line) => FORBIDDEN_TEXT.test(line))).toEqual([]);
        });
    }

    test('layout a 1440: stepper x=72 w=1296, contenuto x=512 w=856, wizard centrato', async ({ openStep }) => {
        const { page } = await openStep('it', 1440);
        const rect = (selector) => page.locator(selector).first().evaluate((el) => { const r = el.getBoundingClientRect(); return { x: Math.round(r.left), w: Math.round(r.width) }; });
        const stepper = await rect('.cmp-wizard-widget .steppers');
        const content = await rect('form.fi-sc-wizard-step.fi-active .steppers-content');
        expect(stepper, 'stepper (riferimento x=72 w=1296)').toEqual({ x: 72, w: 1296 });
        expect(content, '.steppers-content del passo attivo (riferimento x=512 w=856)').toEqual({ x: 512, w: 856 });
        expect(Math.abs(stepper.x + stepper.w / 2 - 720), 'centro dello stepper rispetto al viewport 1440').toBeLessThanOrEqual(2);
    });

    for (const width of VIEWPORTS) {
        test(`a ${width}px: nessuno scorrimento orizzontale e bersagli di almeno ${width < 992 ? TARGET_MIN_PX_COARSE : TARGET_MIN_PX_FINE} px`, async ({ openStep }) => {
            const targetMin = width < 992 ? TARGET_MIN_PX_COARSE : TARGET_MIN_PX_FINE;
            const { page } = await openStep('it', width, { touch: width < 992 });
            await page.waitForTimeout(1000);
            const overflow = await page.evaluate(() => ({ page: document.documentElement.scrollWidth - document.documentElement.clientWidth, widget: (document.querySelector('.cmp-wizard-widget')?.scrollWidth ?? 0) - document.documentElement.clientWidth }));
            expect(overflow.page, 'scorrimento orizzontale della pagina (px)').toBeLessThanOrEqual(0);
            expect(overflow.widget, 'il widget del wizard sborda dal viewport (px)').toBeLessThanOrEqual(0);
            const small = await page.evaluate((min) => {
                const own = 'form.fi-sc-wizard-step.fi-active, .dc-wizard-footer, .steppers';
                const selector = 'button, a[href], input:not([type=hidden]):not([type=checkbox]), select, textarea, [role=button], [role=combobox]';
                return [...document.querySelectorAll(own)].flatMap((root) => [...root.querySelectorAll(selector)])
                    .filter((el) => el.checkVisibility?.() !== false && !el.closest('.leaflet-control-attribution, .phpdebugbar, [aria-hidden=true]'))
                    .map((el) => { const box = (el.closest('.fi-input-wrp') ?? el).getBoundingClientRect(); return { el, w: Math.round(box.width), h: Math.round(box.height) }; })
                    .filter(({ w, h }) => w > 1 && h > 1 && (w < min || h < min))
                    .map(({ el, w, h }) => `${el.tagName.toLowerCase()}${el.id ? `#${el.id}` : ''}.${String(el.className).split(/\s+/)[0]} ${w}x${h} "${(el.getAttribute('aria-label') || el.innerText || el.placeholder || '').trim().slice(0, 24)}"`);
            }, targetMin);
            expect(small, `bersagli sotto ${targetMin}px`).toEqual([]);
        });
    }
});

#!/usr/bin/env node
// Browser smoke test.
//
// The PHP suite asserts rendered HTML, which is why it never noticed that the
// shell markup was perfect and completely inert: mountShell() bails unless
// body[data-shell="admin"] is present, and the layout was not emitting it. No
// amount of assertions about markup catches markup that does nothing.
//
// This drives a real browser against a real server and checks that things
// actually respond to a click.
//
// It runs against a real application rather than a bare testbench skeleton,
// because the thing under test is the compiled bundle — a skeleton has no Vite
// build, so @vite would throw before a single line of JavaScript ran. Any app
// with the package installed and `npm run build` done will do; docs/installation.md
// stands one up in six commands.
//
// Usage:
//   npm run smoke                        # against http://127.0.0.1:8000
//   npm run smoke -- --url http://…      # against a running app
//
// Exit codes: 0 all checks passed · 1 a check failed · 2 could not reach the app.

import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const { chromium } = require('playwright');

const args = process.argv.slice(2);
const urlFlag = args.indexOf('--url');
const BASE = (urlFlag !== -1 ? args[urlFlag + 1] : 'http://127.0.0.1:8000').replace(/\/$/, '');

const results = [];
const record = (name, ok, detail) => {
  results.push({ name, ok, detail });
  process.stdout.write(`${ok ? '  ✓' : '  ✗'} ${name}${detail ? ` — ${detail}` : ''}\n`);
};

const reachable = await fetch(`${BASE}/demo/index`).then((r) => r.ok).catch(() => false);
if (!reachable) {
  process.stderr.write(`cannot reach ${BASE}/demo/index\n`);
  process.stderr.write('Start an app with the package installed, demo mode on, and assets built.\n');
  process.exit(2);
}

const browser = await chromium.launch();
const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
const page = await context.newPage();

const jsErrors = [];
// Chromium logs this when a navigation interrupts a view transition, which is
// exactly what submitting a form does. It describes the browser's own state,
// not the page's. Everything else counts.
const BENIGN = /Transition was aborted because of invalid state/i;

const noteError = (text) => { if (!BENIGN.test(text)) {jsErrors.push(text);} };

page.on('pageerror', (e) => noteError(e.message));
page.on('console', (m) => { if (m.type() === 'error') {noteError(m.text());} });

await page.goto(`${BASE}/demo/index`, { waitUntil: 'networkidle' });

// The attribute the whole design system keys off.
record(
  'body carries data-shell="admin"',
  await page.getAttribute('body', 'data-shell') === 'admin'
);

// Sidebar accordion.
// Assert the group that was clicked opened. Counting open groups would not
// work: the sidebar behaves as an accordion, so opening one closes another and
// the total stays put.
const closedToggle = await page.$('.nav-tree:not(.open) .nav-toggle');
if (closedToggle) {
  await closedToggle.click();
  await page.waitForTimeout(300);
  const opened = await closedToggle.evaluate((el) => el.closest('.nav-tree').classList.contains('open'));
  const expanded = await closedToggle.getAttribute('aria-expanded');
  record('sidebar accordion opens the group clicked', opened, `aria-expanded=${expanded}`);
} else {
  record('sidebar accordion opens the group clicked', false, 'no closed group to open');
}

// Theme toggle.
const themeBefore = await page.getAttribute('html', 'data-theme');
await page.click('.theme-toggle');
await page.waitForTimeout(250);
const themeAfter = await page.getAttribute('html', 'data-theme');
record('theme toggle flips the theme', themeBefore !== themeAfter, `${themeBefore} -> ${themeAfter}`);
await page.click('.theme-toggle');

// Account menu, and where it actually points.
await page.click('.tb-avatar');
await page.waitForTimeout(300);
const menuItems = await page.$$eval('.menu-item', (els) => els.map((e) => e.textContent.trim()));
record('account menu opens', menuItems.length > 0, `${menuItems.length} items`);
await page.keyboard.press('Escape');

// Notification panel.
await page.click('.tb-notifications');
await page.waitForTimeout(300);
record('notifications panel opens', await page.$$eval('.menu-panel', (e) => e.length) > 0);
await page.keyboard.press('Escape');

// Command palette, and that it offers this app's pages rather than the
// static template's demo pages.
await page.keyboard.press('Meta+k');
const palette = await page.waitForSelector('.cmdk-dialog', { timeout: 3000 }).catch(() => null);
record('command palette opens', palette !== null);

if (palette) {
  const island = await page.$eval('#gentelella-shell-config', (e) => JSON.parse(e.textContent)).catch(() => null);
  record('shell config island is present', island !== null);
  record(
    'palette links resolve to app routes, not .html',
    Boolean(island) && island.pages.length > 0 && !island.pages.some((p) => p.href.includes('.html')),
    island ? `${island.pages.length} pages` : ''
  );

  await page.keyboard.type('advanced');
  await page.waitForTimeout(300);
  const hits = await page.$$eval('.cmdk-item', (els) => els.length);
  record('palette matches a menu item', hits > 0, `${hits} result(s)`);

  // Wait for the navigation rather than reading the URL straight after the
  // keypress — the assignment to window.location has not resolved yet.
  const navigated = await Promise.all([
    page.waitForURL((u) => u.toString() !== `${BASE}/demo/index`, { timeout: 5000 }).then(() => true, () => false),
    page.keyboard.press('Enter'),
  ]).then(([ok]) => ok);

  record('palette result navigates', navigated && !page.url().includes('.html'), page.url());
}

// Forms have to actually submit. The design system fakes a submit on its own
// demo forms, and until it learned to tell them apart it swallowed every real
// one — sign-in, registration and every create screen looked like they did
// nothing. No amount of markup assertions catches that.
await page.goto(`${BASE}/login`, { waitUntil: 'networkidle' });
if (await page.$('form[action$="/login"]')) {
  const posted = await Promise.all([
    page.waitForResponse((r) => r.request().method() === 'POST' && r.url().includes('/login'), { timeout: 5000 })
      .then(() => true, () => false),
    page.click('button[type="submit"]'),
  ]).then(([ok]) => ok);

  record('sign-in form submits to the server', posted);

  // If that signed us in, going back to the sign-in screen must not bounce.
  // Laravel's guest middleware sends an authenticated visitor away from
  // /login; if it sends them somewhere that redirects back, the browser loops
  // until it gives up. That is a two-route interaction no unit test sees.
  await page.waitForLoadState('networkidle').catch(() => {});

  if (!page.url().endsWith('/login')) {
    const settled = await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded', timeout: 10000 })
      .then(() => true, () => false);

    record('signed-in visitor is not bounced in a loop', settled, settled ? page.url() : 'too many redirects');
  } else {
    record('signed-in visitor is not bounced in a loop', true, 'sign-in did not succeed; nothing to loop');
  }
} else {
  record('sign-in form submits to the server', false, 'no login form found');
  record('signed-in visitor is not bounced in a loop', false, 'no login form found');
}

// Signing out has to look like it did something. It used to land back on the
// dashboard — the session really was cleared, but the page was identical and
// the button read as broken.
if (!page.url().endsWith('/login')) {
  await page.click('.tb-avatar').catch(() => {});
  await page.waitForTimeout(300);

  const signOut = await page.$('.menu-item:has-text("Sign out")');

  if (signOut) {
    await signOut.click();
    await page.waitForTimeout(600);

    for (const button of await page.$$('button')) {
      const label = ((await button.textContent()) || '').trim();
      if (/^sign out$/i.test(label) && await button.isVisible()) { await button.click(); break; }
    }

    await page.waitForTimeout(2500);
    record('signing out lands on the sign-in screen', page.url().includes('/login'), page.url());
  } else {
    record('signing out lands on the sign-in screen', false, 'no Sign out item in the account menu');
  }
} else {
  record('signing out lands on the sign-in screen', true, 'not signed in; nothing to sign out of');
}

// The CRUD page loads its rows over the wire.
await page.goto(`${BASE}/demo/tables`, { waitUntil: 'networkidle' });
await page.waitForTimeout(800);
const rows = await page.$$eval('table[data-datatable] tbody tr', (els) => els.length);
record('server-side table renders rows', rows > 0, `${rows} rows`);

// Filters have to reach the server and come back with fewer rows. Sending them
// is the part that broke: DataTables 3 never invokes the ajax.data callback, so
// the filters go into the endpoint URL instead.
const filterControl = await page.$('select[data-table-filter]');
if (filterControl) {
  const before = await page.$$eval('table[data-datatable] tbody tr', (e) => e.length);
  const value = await filterControl.$eval('option:nth-child(2)', (o) => o.value);
  await filterControl.selectOption(value);
  await page.waitForTimeout(900);
  const after = await page.$$eval('table[data-datatable] tbody tr', (e) => e.length);
  record('filter narrows the result set', after < before, `${before} -> ${after} rows`);

  await page.click('[data-table-filter-reset]');
  await page.waitForTimeout(900);
  const reset = await page.$$eval('table[data-datatable] tbody tr', (e) => e.length);
  record('clearing filters restores the rows', reset === before, `${after} -> ${reset} rows`);
} else {
  record('filter narrows the result set', false, 'no filter control on the page');
}

record('no JavaScript errors', jsErrors.length === 0, jsErrors.join(' | '));

await browser.close();

const failed = results.filter((r) => !r.ok);
process.stdout.write(`\n${results.length - failed.length}/${results.length} checks passed\n`);
process.exit(failed.length === 0 ? 0 : 1);

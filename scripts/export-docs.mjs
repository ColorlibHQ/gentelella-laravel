#!/usr/bin/env node
// Export this package's documentation into the Gentelella docs site.
//
// gentelella.colorlib.com/docs is the single source of truth for readers, and
// this repo's docs/ is the source of truth for authors — it sits next to the
// code and is checked against it by tests/Feature/DocumentationTest.php. Two
// hand-written copies would drift, so the site's Laravel section is generated
// from these files.
//
// Usage:
//   node scripts/export-docs.mjs --out ../tailwind-templates/templates/gentelella-landing
//   node scripts/export-docs.mjs --dry-run
//
// Exit codes: 0 success · 1 validation error.

import { readFileSync, writeFileSync, existsSync, mkdirSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const DEFAULT_OUT = resolve(ROOT, '..', 'tailwind-templates', 'templates', 'gentelella-landing');

// Which package doc becomes which site page, in reading order. The title and
// description are written here rather than derived: they are the page's <title>
// and meta description on a public site, and deserve to be chosen.
const PAGES = [
  {
    file: 'installation.md',
    slug: 'laravel',
    nav: 'Overview',
    title: 'Laravel',
    description:
      'Gentelella v4 as a Laravel package. Blade layouts, a config-driven sidebar, 25 components and a CRUD engine with server-side tables — vanilla JS and SCSS, no Bootstrap, no jQuery.',
  },
  {
    file: 'configuration.md',
    slug: 'laravel-configuration',
    nav: 'Configuration',
    title: 'Laravel configuration',
    description: 'Every key in config/gentelella.php — branding, routing, the sidebar menu, the topbar, authentication and the bundled demo.',
  },
  {
    file: 'layout.md',
    slug: 'laravel-layout',
    nav: 'Layout',
    title: 'Laravel layout',
    description: 'Extending the admin shell from Blade: page sections, breadcrumbs, bare layouts for auth screens, theming, and the two attributes the design system needs.',
  },
  {
    file: 'menu.md',
    slug: 'laravel-menu',
    nav: 'Menu',
    title: 'Laravel menu',
    description: 'The config-driven sidebar: item shapes, route and URL targets, permission filters, and the icon set generated from the HTML edition.',
  },
  {
    file: 'components.md',
    slug: 'laravel-components',
    nav: 'Components',
    title: 'Laravel components',
    description: '25 Blade components extracted from the template’s own markup — cards, stats, tables, badges, banners and form controls.',
  },
  {
    file: 'crud.md',
    slug: 'laravel-crud',
    nav: 'CRUD panels',
    title: 'Laravel CRUD',
    description: 'Describe an admin screen in one setup() method: model, columns, fields, filters. Server-side tables, seven operations, CSV export and a generator that reads your schema.',
  },
  {
    file: 'columns.md',
    slug: 'laravel-columns',
    nav: 'Column types',
    title: 'Laravel column types',
    description: 'The 14 list column types — text, money, status, relationship, progress, actions and the rest — and the options each one takes.',
  },
  {
    file: 'fields.md',
    slug: 'laravel-fields',
    nav: 'Field types',
    title: 'Laravel field types',
    description: 'The 28 form field types, from native inputs to multi-select, rich text, date ranges, uploads and repeatable groups.',
  },
  {
    file: 'authentication.md',
    slug: 'laravel-auth',
    nav: 'Authentication',
    title: 'Laravel authentication',
    description: 'Sign-in, registration and password reset on the template’s auth markup, with throttling and no account enumeration — and how it stays out of the way of auth you already have.',
  },
  {
    file: 'demo.md',
    slug: 'laravel-demo',
    nav: 'Bundled demo',
    title: 'Laravel demo',
    description: 'All 58 pages of the HTML edition served from your own app, one of them backed by a real CRUD panel over seeded data.',
  },
  {
    file: 'errors-and-localisation.md',
    slug: 'laravel-errors',
    nav: 'Errors & i18n',
    title: 'Laravel error pages and i18n',
    description: 'Publishing the error views Laravel will actually use, what a 5xx page must never print, and translating every string the package renders.',
  },
  {
    file: 'commands.md',
    slug: 'laravel-commands',
    nav: 'Commands',
    title: 'Laravel commands',
    description: 'Every Artisan command and publish tag the package adds: install, crud, make-auth and demo.',
  },
  {
    file: 'deployment.md',
    slug: 'laravel-deployment',
    nav: 'Deployment',
    title: 'Laravel deployment',
    description: 'Running the Laravel edition on a real PHP host — server requirements, nginx, production caches, and the redirect that will bite you behind a CDN.',
  },
];

const BY_FILE = new Map(PAGES.map((p) => [p.file, p]));

const HELP = (extra) => {
  if (extra) {process.stderr.write(`error: ${extra}\n\n`);}
  process.stderr.write(
    'Usage: node scripts/export-docs.mjs [--out <docs-site>] [--dry-run]\n\n' +
    'Writes <out>/src/content/docs/laravel*.md and prints the nav section\n' +
    'to add to src/data/docs-nav.ts.\n'
  );
};

function parseArgs(argv) {
  const opts = { out: DEFAULT_OUT, dryRun: false, help: false };
  for (let i = 0; i < argv.length; i += 1) {
    const a = argv[i];
    if (a === '--help' || a === '-h') {opts.help = true;}
    else if (a === '--dry-run') {opts.dryRun = true;}
    else if (a === '--out') {opts.out = resolve(argv[++i] ?? '');}
    else {return { error: `unknown argument: ${a}` };}
  }
  return opts;
}

const yaml = (s) => `"${String(s).replace(/\\/g, '\\\\').replace(/"/g, '\\"')}"`;

/**
 * Rewrite the package's relative links for the site.
 *
 * README.md is the package's own index and has no page here, so links to it
 * become the section's first page. Anything pointing at a file with no mapping
 * is left alone rather than guessed at — a wrong link is worse than a relative
 * one that simply does not resolve.
 */
function rewriteLinks(markdown, sourceFile) {
  const unmapped = [];

  const out = markdown.replace(/\]\(([A-Za-z0-9._-]+\.md)(#[^)]*)?\)/g, (match, file, hash = '') => {
    if (file === 'README.md') {return `](/docs/${PAGES[0].slug}/${hash})`;}

    const page = BY_FILE.get(file);
    if (!page) {unmapped.push(`${sourceFile} -> ${file}`); return match;}

    return `](/docs/${page.slug}/${hash})`;
  });

  return { out, unmapped };
}

/** Drop the H1: the site's layout renders the title from frontmatter. */
function stripLeadingHeading(markdown, title) {
  return markdown.replace(/^#\s+.*\n+/, '').trimStart() || `# ${title}\n`;
}

const opts = parseArgs(process.argv.slice(2));
if (opts.error) {HELP(opts.error); process.exit(1);}
if (opts.help) {HELP(); process.exit(0);}

const today = new Date().toISOString().slice(0, 10);
const written = [];
const unmappedAll = [];

for (const page of PAGES) {
  const source = resolve(ROOT, 'docs', page.file);

  if (!existsSync(source)) {HELP(`missing docs/${page.file}`); process.exit(1);}

  const raw = readFileSync(source, 'utf8');
  const { out, unmapped } = rewriteLinks(raw, page.file);
  unmappedAll.push(...unmapped);

  const body = stripLeadingHeading(out, page.title);

  written.push({
    slug: page.slug,
    contents:
      `---\ntitle: ${yaml(page.title)}\ndescription: ${yaml(page.description)}\nupdated: ${today}\n---\n\n` +
      `> The Laravel edition is a Composer package — [ColorlibHQ/gentelella-laravel](https://github.com/ColorlibHQ/gentelella-laravel).\n` +
      `> **[Live demo →](https://gentelella-laravel.colorlib.com)**\n\n` +
      body,
  });
}

const width = Math.max(...PAGES.map((p) => p.nav.length));

const navSection = [
  '  {',
  "    label: 'Laravel',",
  '    items: [',
  ...PAGES.map((p) => `      { text: ${`'${p.nav}',`.padEnd(width + 3)} slug: '${p.slug}' },`),
  '    ],',
  '  },',
].join('\n');

if (opts.dryRun) {
  process.stderr.write(`would write ${written.length} pages to ${resolve(opts.out, 'src/content/docs')}\n`);
  for (const u of unmappedAll) {process.stderr.write(`  unmapped link: ${u}\n`);}
  process.stdout.write(`\nAdd to src/data/docs-nav.ts:\n\n${navSection}\n`);
  process.exit(0);
}

const dir = resolve(opts.out, 'src', 'content', 'docs');
if (!existsSync(dir)) {HELP(`no docs collection at ${dir}`); process.exit(1);}

mkdirSync(dir, { recursive: true });
for (const { slug, contents } of written) {
  writeFileSync(resolve(dir, `${slug}.md`), contents);
}

process.stdout.write(`wrote ${written.length} pages -> ${dir}\n`);
for (const u of unmappedAll) {process.stdout.write(`  unmapped link: ${u}\n`);}

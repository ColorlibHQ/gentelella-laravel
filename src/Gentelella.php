<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella;

use ColorlibHQ\Gentelella\Menu\MenuBuilder;
use ColorlibHQ\Gentelella\Menu\MenuResult;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Router;

/**
 * The package's public surface: menu, icons, breadcrumbs and page title.
 *
 * Resolved as a singleton, so the menu is built at most once per page key per
 * request even though the layout, the sidebar partial and the command palette
 * all ask for it.
 */
class Gentelella
{
    /**
     * Package version, shown in the footer.
     *
     * Tracks the Laravel package, not the upstream template — the two release
     * on their own cadence.
     */
    public const VERSION = '0.1.0';

    /** @var array<string, MenuResult> */
    private array $menus = [];

    /** @var array<string, string>|null */
    private ?array $icons = null;

    public function __construct(
        private readonly MenuBuilder $builder,
        private readonly Config $config,
        private readonly Router $router,
        private readonly UrlGenerator $url,
    ) {}

    /**
     * The host configuration the design system reads from a JSON island.
     *
     * Without it the Cmd+K palette offers the static template's demo pages and
     * the account menu links to profile.html — neither of which exists in an
     * application. This hands over the real menu and the real URLs.
     *
     * @return array{links: array<string, string>, pages: list<array{label: string, section: string, href: string}>}
     */
    public function shellConfig(string $activeKey = ''): array
    {
        $links = [];

        foreach ((array) $this->config->get('gentelella.links', []) as $name => $target) {
            $resolved = $this->resolveLink($target);

            if ($resolved !== null) {
                $links[(string) $name] = $resolved;
            }
        }

        $pages = array_map(
            fn (array $leaf): array => [
                'label' => $leaf['text'],
                'section' => $leaf['group'],
                'href' => $leaf['href'],
            ],
            $this->menu($activeKey)->searchable,
        );

        return ['links' => $links, 'pages' => $pages];
    }

    /**
     * A route name, or an absolute path or URL. Anything else is dropped rather
     * than guessed at — a menu entry pointing nowhere is worse than one absent.
     */
    private function resolveLink(mixed $target): ?string
    {
        if (! is_string($target) || $target === '') {
            return null;
        }

        if ($this->router->has($target)) {
            return $this->url->route($target);
        }

        return str_starts_with($target, '/') || str_starts_with($target, 'http')
            ? $target
            : null;
    }

    public function menu(string $activeKey = ''): MenuResult
    {
        return $this->menus[$activeKey] ??= $this->builder->build($activeKey);
    }

    /**
     * Inline SVG sidebar icons, generated from ICONS in the upstream template.
     *
     * @return array<string, string>
     */
    public function icons(): array
    {
        /** @var array<string, string> $icons */
        $icons = $this->icons ??= require __DIR__.'/../resources/icons.php';

        return $icons;
    }

    /**
     * One icon's SVG markup, or an empty string when the name is unknown — a
     * missing icon should leave a gap in the sidebar, not raise.
     */
    public function icon(?string $name): string
    {
        if ($name === null) {
            return '';
        }

        return $this->icons()[$name] ?? '';
    }

    /**
     * Parse a breadcrumb string into renderable crumbs.
     *
     * Same grammar as the static template's data-breadcrumb attribute:
     *
     *     "Home > Projects|/projects > Acme Redesign"
     *
     * Every crumb but the last resolves to a link, in this order: an explicit
     * target after '|', then an exact label match against the menu, then plain
     * text. The last crumb is the current page — never a link.
     *
     * @return list<array{text: string, href: string|null, current: bool}>
     */
    public function breadcrumb(string $raw, string $activeKey = ''): array
    {
        $segments = array_values(array_filter(
            array_map(trim(...), explode('>', $raw)),
            static fn (string $s): bool => $s !== '',
        ));

        if ($segments === []) {
            $segments = ['Home'];
        }

        $hrefs = $this->crumbHrefs($activeKey);
        $last = count($segments) - 1;
        $crumbs = [];

        foreach ($segments as $i => $segment) {
            $bar = strpos($segment, '|');
            $text = $bar === false ? $segment : trim(substr($segment, 0, $bar));
            $explicit = $bar === false ? null : trim(substr($segment, $bar + 1));

            $crumbs[] = [
                'text' => $text,
                'href' => $i === $last ? null : ($explicit ?? $hrefs[$text] ?? null),
                'current' => $i === $last,
            ];
        }

        return $crumbs;
    }

    /**
     * The full <title>: "<section> | <prefix><title><postfix>".
     */
    public function title(?string $section = null): string
    {
        $base = (string) $this->config->get('gentelella.title_prefix', '')
            .(string) $this->config->get('gentelella.title', 'Gentelella')
            .(string) $this->config->get('gentelella.title_postfix', '');

        return $section === null || $section === '' ? $base : $section.' | '.$base;
    }

    /**
     * Label -> href for breadcrumbs, with "Home" seeded to the dashboard the way
     * the static template hand-seeds it.
     *
     * @return array<string, string>
     */
    private function crumbHrefs(string $activeKey): array
    {
        $map = $this->menu($activeKey)->crumbHrefs();

        if (! isset($map['Home'])) {
            $first = $this->menu($activeKey)->groups[0]['items'][0] ?? null;
            $home = $first['href'] ?? ($first['children'][0]['href'] ?? null);

            if (is_string($home)) {
                $map['Home'] = $home;
            }
        }

        return $map;
    }
}

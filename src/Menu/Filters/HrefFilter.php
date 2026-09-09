<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu\Filters;

use ColorlibHQ\Gentelella\Menu\MenuContext;
use ColorlibHQ\Gentelella\Menu\MenuFilter;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Router;

/**
 * Resolves an item's target to a concrete href.
 *
 * Precedence: an explicit 'url' wins, then a named 'route', then 'page' — the
 * demo-page slug carried over from the static template's NAV. A parent with
 * children has no target of its own and is left alone.
 *
 * An unresolvable target yields '#' rather than throwing: a menu entry pointing
 * at a route the app has not defined should render as inert, not 500 the page.
 */
final class HrefFilter implements MenuFilter
{
    public function __construct(
        private readonly UrlGenerator $url,
        private readonly Router $router,
    ) {}

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function apply(array $item, MenuContext $context): array
    {
        if (isset($item['children'])) {
            return $item;
        }

        $item['href'] = $this->resolve($item);

        return $item;
    }

    /** @param array<string, mixed> $item */
    private function resolve(array $item): string
    {
        if (isset($item['url'])) {
            return (string) $item['url'];
        }

        if (isset($item['route'])) {
            $name = (string) $item['route'];

            return $this->router->has($name)
                ? $this->url->route($name, (array) ($item['route_params'] ?? []))
                : '#';
        }

        if (isset($item['page'])) {
            $name = 'gentelella.demo.'.$item['page'];

            if ($this->router->has($name)) {
                return $this->url->route($name);
            }

            // A demo page backed by a CRUD panel registers its list screen under
            // the conventional '.index' suffix rather than the bare page name.
            if ($this->router->has($name.'.index')) {
                return $this->url->route($name.'.index');
            }

            return '#';
        }

        return '#';
    }
}

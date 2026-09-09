<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;

/**
 * Turns the menu definition into a renderable tree.
 *
 * Children are resolved depth-first so that by the time a parent reaches
 * ActiveFilter its children already carry their `active` flags and the parent's
 * open state can be read straight off them. A parent whose children were all
 * dropped (by GateFilter, say) is dropped too, so no empty submenus render.
 */
final class MenuBuilder
{
    /** @var list<MenuFilter>|null */
    private ?array $filters = null;

    public function __construct(
        private readonly Config $config,
        private readonly Container $container,
    ) {}

    public function build(string $activeKey = ''): MenuResult
    {
        $context = new MenuContext($activeKey);
        $groups = [];

        foreach ($this->definition() as $group) {
            $context->enterGroup((string) $group['label']);
            $items = [];

            foreach ($group['items'] as $item) {
                $filtered = $this->applyFilters($item, $context);

                if ($filtered !== null) {
                    $items[] = $filtered;
                }
            }

            if ($items !== []) {
                $groups[] = ['label' => (string) $group['label'], 'items' => $items];
            }
        }

        return new MenuResult($groups, $context->searchable());
    }

    /**
     * The configured menu, falling back to the bundled demo sidebar. That
     * fallback is generated from NAV in the upstream template — see
     * `npm run export:php` in ColorlibHQ/gentelella.
     *
     * @return list<array{label: string, items: list<array<string, mixed>>}>
     */
    private function definition(): array
    {
        $menu = $this->config->get('gentelella.menu');

        if (is_array($menu)) {
            return $menu;
        }

        return require __DIR__.'/../../resources/menu.php';
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    private function applyFilters(array $item, MenuContext $context): ?array
    {
        if (isset($item['children']) && is_array($item['children'])) {
            $children = [];

            foreach ($item['children'] as $child) {
                $filtered = $this->applyFilters($child, $context);

                if ($filtered !== null) {
                    $children[] = $filtered;
                }
            }

            if ($children === []) {
                return null;
            }

            $item['children'] = $children;
        }

        foreach ($this->filters() as $filter) {
            $item = $filter->apply($item, $context);

            if ($item === null) {
                return null;
            }
        }

        return $item;
    }

    /** @return list<MenuFilter> */
    private function filters(): array
    {
        if ($this->filters !== null) {
            return $this->filters;
        }

        $filters = [];

        foreach ((array) $this->config->get('gentelella.filters', []) as $class) {
            $filter = $this->container->make($class);

            if ($filter instanceof MenuFilter) {
                $filters[] = $filter;
            }
        }

        return $this->filters = $filters;
    }
}

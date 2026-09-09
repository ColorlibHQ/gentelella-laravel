<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu;

/**
 * The outcome of one menu build: the renderable tree, plus the flattened leaf
 * list SearchFilter gathered along the way.
 *
 * @phpstan-type MenuGroup array{label: string, items: list<array<string, mixed>>}
 */
final class MenuResult
{
    /**
     * @param  list<MenuGroup>  $groups
     * @param  list<array{text: string, href: string, group: string}>  $searchable
     */
    public function __construct(
        public readonly array $groups,
        public readonly array $searchable,
    ) {}

    /**
     * Label -> href lookup for breadcrumb resolution.
     *
     * Mirrors CRUMB_HREFS in the static template: a parent group resolves to its
     * first child — the page the sidebar opens when you click the group — and the
     * first occurrence of a label wins, so an earlier group beats a later one.
     *
     * @return array<string, string>
     */
    public function crumbHrefs(): array
    {
        $map = [];

        foreach ($this->groups as $group) {
            foreach ($group['items'] as $item) {
                $children = $item['children'] ?? [];
                $href = $item['href'] ?? ($children[0]['href'] ?? null);

                if (is_string($href) && ! isset($map[$item['text']])) {
                    $map[$item['text']] = $href;
                }

                foreach ($children as $child) {
                    if (isset($child['href']) && ! isset($map[$child['text']])) {
                        $map[$child['text']] = (string) $child['href'];
                    }
                }
            }
        }

        return $map;
    }
}

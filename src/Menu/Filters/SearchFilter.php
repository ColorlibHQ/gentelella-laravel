<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu\Filters;

use ColorlibHQ\Gentelella\Menu\MenuContext;
use ColorlibHQ\Gentelella\Menu\MenuFilter;

/**
 * Collects every reachable leaf into the context.
 *
 * Runs last, so it only sees items that survived GateFilter and already carry a
 * resolved href. The result feeds the Cmd+K command palette and the breadcrumb
 * label -> href lookup, so neither has to walk the tree again.
 */
final class SearchFilter implements MenuFilter
{
    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function apply(array $item, MenuContext $context): array
    {
        $href = $item['href'] ?? null;

        if (! isset($item['children']) && is_string($href) && $href !== '#') {
            $context->addSearchable((string) $item['text'], $href);
        }

        return $item;
    }
}

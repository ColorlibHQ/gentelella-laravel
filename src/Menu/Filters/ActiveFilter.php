<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu\Filters;

use ColorlibHQ\Gentelella\Menu\MenuContext;
use ColorlibHQ\Gentelella\Menu\MenuFilter;

/**
 * Marks the current item active.
 *
 * A leaf is active when its 'key' matches the page key. A parent is never
 * "active" itself — it is 'open', which is what expands the submenu and mirrors
 * the .has-active class the static template's renderNavItem() emits.
 *
 * Children are already filtered by the time a parent reaches this filter, so
 * the open state can be read straight off them.
 */
final class ActiveFilter implements MenuFilter
{
    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function apply(array $item, MenuContext $context): array
    {
        if (isset($item['children'])) {
            $children = is_array($item['children']) ? $item['children'] : [];
            $open = false;

            foreach ($children as $child) {
                if (is_array($child) && ($child['active'] ?? false) === true) {
                    $open = true;
                    break;
                }
            }

            $item['open'] = $open;

            return $item;
        }

        $item['active'] = isset($item['key'])
            && $context->activeKey !== ''
            && $item['key'] === $context->activeKey;

        return $item;
    }
}

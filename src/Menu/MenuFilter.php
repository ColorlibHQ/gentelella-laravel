<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu;

/**
 * A transformation applied to every menu item before it is rendered.
 *
 * Filters run in the order given by config('gentelella.filters'). Returning
 * null drops the item (and, for a parent, its whole subtree) from the sidebar.
 */
interface MenuFilter
{
    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    public function apply(array $item, MenuContext $context): ?array;
}

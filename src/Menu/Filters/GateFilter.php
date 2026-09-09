<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu\Filters;

use ColorlibHQ\Gentelella\Menu\MenuContext;
use ColorlibHQ\Gentelella\Menu\MenuFilter;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Drops items the current user is not authorised to see.
 *
 * Opt in per item with 'can' => 'ability' (or a list of abilities, all of which
 * must pass). Items without a 'can' key are always visible — authorisation is
 * explicit, never inferred.
 */
final class GateFilter implements MenuFilter
{
    public function __construct(private readonly Gate $gate) {}

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    public function apply(array $item, MenuContext $context): ?array
    {
        if (! isset($item['can'])) {
            return $item;
        }

        foreach ((array) $item['can'] as $ability) {
            if (! $this->gate->allows($ability, $item['can_model'] ?? [])) {
                return null;
            }
        }

        return $item;
    }
}

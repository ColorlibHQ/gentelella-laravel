<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Menu;

/**
 * Per-build state shared by the menu filters.
 *
 * Carries the active page key down the tree, and collects the flattened list of
 * reachable leaves on the way back up so the command palette and the breadcrumb
 * resolver can both read it without walking the menu a second time.
 */
final class MenuContext
{
    /** @var list<array{text: string, href: string, group: string}> */
    private array $searchable = [];

    private string $group = '';

    public function __construct(public readonly string $activeKey) {}

    public function enterGroup(string $label): void
    {
        $this->group = $label;
    }

    public function currentGroup(): string
    {
        return $this->group;
    }

    public function addSearchable(string $text, string $href): void
    {
        $this->searchable[] = ['text' => $text, 'href' => $href, 'group' => $this->group];
    }

    /** @return list<array{text: string, href: string, group: string}> */
    public function searchable(): array
    {
        return $this->searchable;
    }
}

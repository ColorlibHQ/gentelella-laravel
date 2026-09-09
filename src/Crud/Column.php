<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * One column of the list view.
 *
 * Built from a definition array — ['name' => 'price', 'type' => 'money'] — or
 * from a bare string, which is shorthand for a text column of that name.
 */
class Column
{
    /** Keys consumed by the column itself; anything else is type-specific. */
    private const RESERVED = ['name', 'type', 'label', 'searchable', 'orderable'];

    /** @param array<string, mixed> $options */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $label,
        public readonly bool $searchable,
        public readonly bool $orderable,
        public readonly array $options = [],
    ) {}

    /** @param array<string, mixed>|string $definition */
    public static function fromArray(array|string $definition): self
    {
        $definition = is_string($definition) ? ['name' => $definition] : $definition;

        $name = (string) ($definition['name'] ?? '');

        if ($name === '') {
            throw new \InvalidArgumentException('A column definition needs a name.');
        }

        $type = (string) ($definition['type'] ?? 'text');

        return new self(
            name: $name,
            type: $type,
            label: (string) ($definition['label'] ?? Str::headline($name)),
            searchable: (bool) ($definition['searchable'] ?? false),
            // A relationship column can only be ordered when it resolves to a
            // single owning row; see Panel::orderableColumn().
            orderable: (bool) ($definition['orderable'] ?? true),
            options: Arr::except($definition, self::RESERVED),
        );
    }

    public function isRelationship(): bool
    {
        return $this->type === 'relationship';
    }

    /**
     * The related attribute a relationship column displays — 'title' in
     * ['name' => 'category', 'type' => 'relationship', 'attribute' => 'title'].
     */
    public function attribute(): string
    {
        return (string) ($this->options['attribute'] ?? 'name');
    }

    public function option(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }
}

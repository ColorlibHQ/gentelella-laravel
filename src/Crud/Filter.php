<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * One control in the list view's filter bar.
 *
 * Filters narrow the query in SQL, alongside the search box, and survive paging
 * and sorting because the table sends them with every request.
 */
class Filter
{
    private const RESERVED = ['name', 'type', 'label'];

    /** @param array<string, mixed> $options */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $label,
        public readonly array $options = [],
    ) {}

    /** @param array<string, mixed>|string $definition */
    public static function fromArray(array|string $definition): self
    {
        $definition = is_string($definition) ? ['name' => $definition] : $definition;

        $name = (string) ($definition['name'] ?? '');

        if ($name === '') {
            throw new \InvalidArgumentException('A filter definition needs a name.');
        }

        return new self(
            name: $name,
            type: (string) ($definition['type'] ?? 'text'),
            label: (string) ($definition['label'] ?? Str::headline($name)),
            options: Arr::except($definition, self::RESERVED),
        );
    }

    public function option(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /** The column this filters on, which defaults to the filter's own name. */
    public function column(): string
    {
        return (string) ($this->options['column'] ?? $this->name);
    }
}

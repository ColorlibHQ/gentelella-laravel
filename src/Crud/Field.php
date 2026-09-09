<?php

declare(strict_types=1);

namespace ColorlibHQ\Gentelella\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * One input on the create/edit form.
 *
 * `rules` may be a string, an array, or a closure receiving the record being
 * edited — the closure form is what makes Rule::unique(...)->ignore($entry)
 * possible without rewriting rule strings.
 */
class Field
{
    private const RESERVED = ['name', 'type', 'label', 'rules', 'hint', 'default'];

    /** @param array<string, mixed> $options */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $label,
        public readonly mixed $rules,
        public readonly ?string $hint,
        public readonly mixed $default,
        public readonly array $options = [],
    ) {}

    /** @param array<string, mixed>|string $definition */
    public static function fromArray(array|string $definition): self
    {
        $definition = is_string($definition) ? ['name' => $definition] : $definition;

        $name = (string) ($definition['name'] ?? '');

        if ($name === '') {
            throw new \InvalidArgumentException('A field definition needs a name.');
        }

        return new self(
            name: $name,
            type: (string) ($definition['type'] ?? 'text'),
            label: (string) ($definition['label'] ?? Str::headline($name)),
            // No rules still means "accept it": a field on the form is meant to
            // be saved, and `nullable` keeps it in the validated payload.
            rules: $definition['rules'] ?? 'nullable',
            hint: isset($definition['hint']) ? (string) $definition['hint'] : null,
            default: $definition['default'] ?? null,
            options: Arr::except($definition, self::RESERVED),
        );
    }

    public function option(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function isRequired(): bool
    {
        $rules = $this->rules;

        if (is_string($rules)) {
            return str_contains($rules, 'required');
        }

        if (is_array($rules)) {
            foreach ($rules as $rule) {
                if (is_string($rule) && str_contains($rule, 'required')) {
                    return true;
                }
            }
        }

        return false;
    }
}

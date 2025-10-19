<?php

namespace Sunchayn\Nimbus\Modules\Schemas\ValueObjects;

use Sunchayn\Nimbus\Modules\Schemas\Enums\RulesFieldType;

/**
 * Represents a field path in Laravel validation rules.
 *
 * Handles parsing, validation, and type detection for field paths
 * like "user.profile.age" or "tags.*".
 */
readonly class FieldPath
{
    /** @var string[] */
    public array $segments;

    public function __construct(
        public string $value,
        public RulesFieldType $type,
    ) {
        $this->segments = explode('.', $this->value);
    }

    public static function fromString(string $field): self
    {
        $type = match (true) {
            str_ends_with($field, '.*') => RulesFieldType::ARRAY_OF_PRIMITIVES,
            str_contains($field, '.') => RulesFieldType::DOT_NOTATION,
            default => RulesFieldType::ROOT,
        };

        return new self(
            value: $field,
            type: $type,
        );
    }

    public function getRootField(): string
    {
        return $this->segments[0];
    }
}

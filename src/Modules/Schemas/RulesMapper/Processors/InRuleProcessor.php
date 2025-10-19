<?php

namespace Sunchayn\Nimbus\Modules\Schemas\RulesMapper\Processors;

use BackedEnum;
use Illuminate\Validation\Rules\In;
use UnitEnum;

/**
 * Processes `In` validation rules to extract allowed values for schema generation.
 *
 * @phpstan-import-type SchemaPropertyTypesShape from \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty
 * @phpstan-import-type SchemaPropertyEnumShape from \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty
 */
class InRuleProcessor
{
    /**
     * @return array{type: 'string'|'integer', enum: SchemaPropertyEnumShape|null}
     */
    public static function process(In $in): array
    {
        /** @var array<array-key, scalar|object> $rawValues */
        $rawValues = invade($in)->values; // @phpstan-ignore-line

        // Normalize the values into primitives.
        $values = array_map(
            fn (bool|float|int|object|string $value): float|bool|int|string|null => match (true) {
                is_scalar($value) => $value,
                $value instanceof BackedEnum => $value->value,
                $value instanceof UnitEnum => $value->name,
                default => null,
            },
            $rawValues,
        );

        /** @var array<array-key, scalar> $values */
        $values = array_values(
            array_filter($values), // <- Removes null values.
        );

        if (empty($values)) {
            return ['type' => 'string', 'enum' => null];
        }

        $identityValue = $values[0];

        $type = match (true) {
            is_int($identityValue) => 'integer',
            default => 'string',
        };

        return ['type' => $type, 'enum' => $values];
    }
}

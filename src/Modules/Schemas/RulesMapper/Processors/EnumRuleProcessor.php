<?php

namespace Sunchayn\Nimbus\Modules\Schemas\RulesMapper\Processors;

use BackedEnum;
use Illuminate\Validation\Rules\Enum;
use UnitEnum;

/**
 * Processes `Enum` validation rules to extract enum values for schema generation.
 *
 * @phpstan-import-type SchemaPropertyEnumShape from \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty
 */
class EnumRuleProcessor
{
    /**
     * @return array{type: 'string', enum: SchemaPropertyEnumShape|null}
     */
    public static function process(Enum $rule): array
    {
        /** @var class-string<UnitEnum> $enumClass */
        $enumClass = invade($rule)->type; // @phpstan-ignore-line

        if (! enum_exists($enumClass)) {
            return ['type' => 'string', 'enum' => null];
        }

        $values = array_map(
            fn (UnitEnum|BackedEnum $enum): int|string => $enum->value ?? $enum->name,
            $enumClass::cases()
        );

        if ($values === []) {
            return ['type' => 'string', 'enum' => null];
        }

        return ['type' => 'string', 'enum' => $values];
    }
}

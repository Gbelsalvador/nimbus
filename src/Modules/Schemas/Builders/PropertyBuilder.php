<?php

namespace Sunchayn\Nimbus\Modules\Schemas\Builders;

use Sunchayn\Nimbus\Modules\Schemas\RulesMapper\RuleToSchemaMapper;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty;

/**
 * Converts Laravel validation rules into individual schema properties.
 *
 * Maps Laravel rule strings like "required|string|max:255" to JSON Schema properties
 * with proper type, format, and validation constraints.
 *
 * @example
 * Input:  "email" field with "required|email" rules
 * Output: SchemaProperty with type="string", format="email", required=true
 *
 * @phpstan-import-type NormalizedRulesShape from \Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset
 * @phpstan-import-type SchemaPropertyFormatsShape from SchemaProperty
 */
class PropertyBuilder
{
    public function __construct(
        private readonly RuleToSchemaMapper $ruleToSchemaMapper,
    ) {}

    /**
     * @param  NormalizedRulesShape  $rules
     */
    public function buildPropertyFromRules(string $field, array $rules): SchemaProperty
    {
        $schemaMetadata = $this->ruleToSchemaMapper->convertRulesToBaseSchemaPropertyMetadata($rules);

        return new SchemaProperty(
            name: $field,
            type: $schemaMetadata['type'],
            required: $schemaMetadata['required'],
            format: $this->extractFormat($rules),
            enum: $schemaMetadata['enum'] ?? null,
            minimum: $schemaMetadata['minimum'],
            maximum: $schemaMetadata['maximum'],
        );
    }

    /**
     * @param  NormalizedRulesShape  $rules
     * @return SchemaPropertyFormatsShape|null
     */
    private function extractFormat(array $rules): ?string
    {
        foreach ($rules as $rule) {
            if (! is_string($rule)) {
                continue;
            }

            $format = $this->detectFormatFromRule($rule);

            if ($format !== null) {
                return $format;
            }
        }

        return null;
    }

    /**
     * @return SchemaPropertyFormatsShape|null
     */
    private function detectFormatFromRule(string $rule): ?string
    {
        return match ($rule) {
            'email' => 'email',
            'uuid' => 'uuid',
            'date' => 'date-time',
            default => null,
        };
    }
}

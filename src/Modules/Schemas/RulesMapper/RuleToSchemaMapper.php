<?php

namespace Sunchayn\Nimbus\Modules\Schemas\RulesMapper;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationRuleParser;
use Sunchayn\Nimbus\Modules\Schemas\RulesMapper\Processors\EnumRuleProcessor;
use Sunchayn\Nimbus\Modules\Schemas\RulesMapper\Processors\InRuleProcessor;

/**
 * Converts Laravel validation rules into JSON Schema property definitions.
 *
 * @phpstan-import-type NormalizedRulesShape from \Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset
 * @phpstan-import-type SchemaPropertyTypesShape from \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty
 * @phpstan-import-type SchemaPropertyFormatsShape from \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty
 * @phpstan-import-type SchemaPropertyEnumShape from \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty
 */
class RuleToSchemaMapper
{
    /**
     * Converts an array of Laravel validation rules into schema property data.
     *
     * Laravel's validation rules are processed sequentially, with later rules
     * potentially overriding earlier ones (e.g., 'string' then 'email').
     *
     * @param  NormalizedRulesShape  $rules
     * @return array{
     *    type: SchemaPropertyTypesShape,
     *    required: bool,
     *    format: SchemaPropertyFormatsShape|null,
     *    enum: SchemaPropertyEnumShape|null,
     *    minimum: ?int,
     *    maximum: ?int,
     *  }
     */
    public function convertRulesToBaseSchemaPropertyMetadata(array $rules): array
    {
        $shape = [
            'type' => 'string',
            'required' => false,
            'format' => null,
            'enum' => null,
            'minimum' => null,
            'maximum' => null,
        ];

        foreach ($rules as $rule) {
            $ruleSpecificUpdates = $this->processRule($rule);

            // Amend the original shape to add the changes specific to the rule in hand.
            $shape = array_merge($shape, $ruleSpecificUpdates);
        }

        return $shape;
    }

    /**
     * Processes individual validation rules and returns the changes to apply.
     *
     * @return array{}|array{
     *     type?: SchemaPropertyTypesShape,
     *     format?: SchemaPropertyFormatsShape,
     *     enum?: SchemaPropertyEnumShape|null,
     *     minimum?: int,
     *     maximum?: int,
     *     }
     */
    private function processRule(mixed $rule): array
    {
        if (is_object($rule)) {
            return $this->processObjectRule($rule);
        }

        if (! is_scalar($rule)) {
            return [];
        }

        [$name, $params] = ValidationRuleParser::parse((string) $rule);

        $ruleName = strtolower($name);

        return match ($ruleName) {
            'required' => ['required' => true],
            'string' => ['type' => 'string'],
            'integer' => ['type' => 'integer'],
            'numeric' => ['type' => 'number'],
            'boolean' => ['type' => 'boolean'],
            'array' => ['type' => 'array'],
            'email' => $this->setFormat('email'),
            'uuid' => $this->setFormat('uuid'),
            'date' => $this->setFormat('date-time'),
            'in' => $this->setEnum($params),
            'min' => ['minimum' => $params[0] ?? null],
            'max' => ['maximum' => $params[0] ?? null],
            'size' => ['minimum' => $params[0] ?? null, 'maximum' => $params[0] ?? null],
            default => [],
        };
    }

    /**
     * Handles custom validation rule objects.
     *
     * Analyzes specific rule types like Enum and In to extract constraint
     * information, falling back to string type for unknown rules.
     *
     * @return array{type: 'integer'|'string', enum?: SchemaPropertyEnumShape|null}
     */
    private function processObjectRule(object $rule): array
    {
        return match (true) {
            $rule instanceof Enum => EnumRuleProcessor::process($rule),
            $rule instanceof In => InRuleProcessor::process($rule),
            default => ['type' => 'string'],
        };
    }

    /**
     * Sets the format specification for the property.
     *
     * @param  SchemaPropertyFormatsShape  $format
     * @return array{format: SchemaPropertyFormatsShape, type?: 'string'}
     */
    private function setFormat(string $format): array
    {
        $result = ['format' => $format];

        // Email, UUID, and date-time are all string-based formats in JSON Schema
        if (in_array($format, ['email', 'uuid', 'date-time'], true)) {
            $result['type'] = 'string';
        }

        return $result;
    }

    /**
     * Sets enum constraints from validation rule parameters.
     *
     * The 'in' rule provides explicit allowed values that must be preserved
     * in the schema for proper validation.
     *
     * @param  array<int, mixed>  $params
     * @return array{enum: SchemaPropertyEnumShape}|array{}
     */
    private function setEnum(array $params): array
    {
        if ($params === []) {
            return [];
        }

        return ['enum' => $params];
    }
}

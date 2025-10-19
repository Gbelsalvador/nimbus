<?php

namespace Sunchayn\Nimbus\Modules\Schemas\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\RulesExtractionError;

/**
 * @phpstan-import-type SchemaPropertyShape from SchemaProperty
 *
 * @phpstan-type SchemaShape array{
 *      '$schema': 'https://json-schema.org/draft/2020-12/schema',
 *      type: 'object',
 *      properties: array<string, SchemaPropertyShape>,
 *      required: string[],
 *      additionalProperties: false,
 *  }
 *
 * @implements Arrayable<string, SchemaPropertyShape>
 */
class Schema implements Arrayable
{
    /** @var SchemaProperty[] */
    public readonly array $properties;

    /**
     * @param  SchemaProperty[]  $properties
     */
    public function __construct(
        array $properties,
        public readonly ?RulesExtractionError $extractionError = null,
    ) {
        $this->properties = array_values($properties);
    }

    public static function empty(): self
    {
        return new self(
            properties: [],
        );
    }

    public function isEmpty(): bool
    {
        return $this->properties === [];
    }

    /**
     * @return string[]
     */
    public function getRequiredProperties(): array
    {
        return collect($this->properties)
            ->filter(fn (SchemaProperty $schemaProperty): bool => $schemaProperty->required)
            ->map(fn (SchemaProperty $schemaProperty): string => $schemaProperty->name)
            ->values()
            ->all();
    }

    public function toArray(): array
    {
        return Arr::mapWithKeys(
            $this->properties,
            fn (SchemaProperty $schemaProperty): array => [
                $schemaProperty->name => $schemaProperty->toArray(),
            ]
        );
    }

    /**
     * Converts this schema to proper JSON Schema format.
     *
     * Generates a complete JSON Schema object with all necessary metadata
     * that can be used directly by JSON Schema validators and editors.
     *
     * @return SchemaShape
     */
    public function toJsonSchema(): array
    {
        return [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'properties' => $this->toArray(),
            'required' => $this->getRequiredProperties(),
            'additionalProperties' => false,
        ];
    }
}

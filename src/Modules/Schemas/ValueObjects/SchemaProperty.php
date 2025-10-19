<?php

namespace Sunchayn\Nimbus\Modules\Schemas\ValueObjects;

use Illuminate\Support\Arr;

// TODO [Refactor] Refactor this into specialized classes with proper support for JSONSchema.
// E.g. IntegerSchemaProperty, ObjectSchemaProperty, etc.
// Most likely its own package.

/**
 * @phpstan-type SchemaPropertyTypesShape 'number'|'integer'|'array'|'object'|'string'|'boolean'
 * @phpstan-type SchemaPropertyFormatsShape 'uuid'|'email'|'date-time'
 * @phpstan-type SchemaPropertyEnumShape array<array-key, scalar>
 * TODO [Documentation] Figure out how to annotate the `items` and `properties` recursively.`
 * @phpstan-type SchemaPropertyShape array{
 *      type: SchemaPropertyTypesShape,
 *      x-name: string,
 *      x-required: bool,
 *      format?: SchemaPropertyFormatsShape,
 *      enum?: SchemaPropertyEnumShape,
 *      items?: array<array-key, mixed>,
 *      properties?: array<string, array<array-key, mixed>>,
 *      required?: bool,
 *      minLength?: int,
 *      minimum?: int,
 *      maxLength?: int,
 *      maximum?: int,
 *  }
 */
class SchemaProperty
{
    /**
     * @param  SchemaPropertyTypesShape  $type
     * @param  SchemaPropertyFormatsShape|null  $format
     * @param  SchemaPropertyEnumShape|null  $enum  Allowed enum values.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type = 'string', // <- Make this an enum.
        public readonly bool $required = false,
        public readonly ?string $format = null, // <- Make this an enum.
        public readonly ?array $enum = null,
        public readonly ?SchemaProperty $itemsSchema = null, // <- For arrays
        public readonly ?Schema $propertiesSchema = null, // <- For objects
        public readonly ?int $minimum = null,
        public readonly ?int $maximum = null,
    ) {}

    /**
     * @return SchemaPropertyShape
     */
    public function toArray(): array
    {
        $result = [
            'type' => $this->type,
        ];

        if ($this->format !== null) {
            $result['format'] = $this->format;
        }

        if ($this->enum !== null && $this->enum !== []) {
            $result['enum'] = $this->enum;
        }

        if ($this->itemsSchema instanceof \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty) {
            $result['items'] = $this->itemsSchema->toArray();
        }

        if ($this->propertiesSchema instanceof \Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema) {
            $result['properties'] = Arr::mapWithKeys(
                $this->propertiesSchema->properties,
                fn (SchemaProperty $schemaProperty): array => [$schemaProperty->name => $schemaProperty->toArray()]
            );

            $result['required'] = $this->propertiesSchema->getRequiredProperties();
        }

        if ($this->minimum !== null && in_array($this->type, ['string', 'integer', 'number'])) {
            $minPropertyName = $this->type === 'string' ? 'minLength' : 'minimum';

            $result[$minPropertyName] = $this->minimum;
        }

        if ($this->maximum !== null && in_array($this->type, ['string', 'integer', 'number'])) {
            $minPropertyName = $this->type === 'string' ? 'maxLength' : 'maximum';

            $result[$minPropertyName] = $this->maximum;
        }

        /**
         * @var SchemaPropertyShape $final PHPStan couldn't infer the correct type when building the array incrementally.
         */
        $final = array_merge(
            $result,
            // To make dealing with props easier, for instance, for payload generation.
            // We also add a couple of custom properties to the schema.
            $this->getCustomProperties(),
        );

        return $final;
    }

    /**
     * @return array{
     *     x-name: string,
     *     x-required: bool,
     * }
     */
    private function getCustomProperties(): array
    {
        return [
            // Keep in mind: the `required` property is reserved for objects to tell what properties are required in the object.
            // this custom property, on the other hand, is to tell if the current property is required or not.
            'x-required' => $this->required,
            'x-name' => $this->name,
        ];
    }
}

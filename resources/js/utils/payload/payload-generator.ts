import { allValueGenerators, PROPERTY_NAME_PATTERNS } from '@/config/generators';
import { PAYLOAD_GENERATOR_CONFIG } from '@/config/payload-generator';
import {
    PayloadObject,
    PayloadObjectValue,
    PayloadPrimitive,
} from '@/interfaces/schema/payload';
import { JsonSchema } from '@/interfaces/schema/shape';
import { ValueGenerator } from '@/interfaces/ui';
import { faker } from '@faker-js/faker';

/**
 * Generates random data based on schema
 */
export function generateRandomPayload(schema: JsonSchema): PayloadObject {
    return generatePayload(schema, false);
}

/**
 * Generates placeholder data based on schema
 */
export function generatePlaceholderPayload(schema: JsonSchema): PayloadObject {
    return generatePayload(schema, true);
}

/**
 * Handles enum value generation
 */
function generateEnumValue(
    schema: JsonSchema,
    isPlaceholder: boolean,
): PayloadObjectValue {
    if (!schema.enum || schema.enum.length === 0) {
        throw new Error('Enum values are required for enum type');
    }

    return isPlaceholder
        ? (schema.enum[0] as PayloadObjectValue)
        : (faker.helpers.arrayElement(schema.enum) as PayloadObjectValue);
}

/**
 * Handles primitive type generation (string, number, boolean)
 */
function generatePrimitiveType(
    schema: JsonSchema,
    isPlaceholder: boolean,
): PayloadObjectValue {
    switch (schema.type) {
        case 'string':
            return generateString(schema, isPlaceholder);
        case 'number':
        case 'integer':
            return generateInteger(schema, isPlaceholder);
        case 'boolean':
            return isPlaceholder ? false : faker.datatype.boolean();
        default:
            throw new Error(`Unsupported primitive type: ${schema.type}`);
    }
}

/**
 * Handles complex type generation (array, object)
 */
function generateComplexType(
    schema: JsonSchema,
    isPlaceholder: boolean,
): PayloadObject | PayloadObject[] | PayloadPrimitive[] {
    switch (schema.type) {
        case 'array':
            return generateArray(schema, isPlaceholder);
        case 'object':
            return schema.properties ? generatePayload(schema, isPlaceholder) : {};
        default:
            throw new Error(`Unsupported complex type: ${schema.type}`);
    }
}

/**
 * Generates a value based on schema property type and format.
 */
function generateValue(
    schema: JsonSchema,
    isPlaceholder: boolean = false,
): PayloadObject | PayloadObjectValue | PayloadObject[] {
    if (schema.enum && schema.enum.length > 0) {
        return generateEnumValue(schema, isPlaceholder);
    }

    // Handle primitive types
    if (
        schema.type &&
        typeof schema.type === 'string' &&
        ['string', 'number', 'integer', 'boolean'].includes(schema.type)
    ) {
        return generatePrimitiveType(schema, isPlaceholder);
    }

    // Handle complex types
    if (
        schema.type &&
        typeof schema.type === 'string' &&
        ['array', 'object'].includes(schema.type)
    ) {
        return generateComplexType(schema, isPlaceholder);
    }

    return null;
}

function generateArrayOfObjects(
    schema: JsonSchema,
    isPlaceholder: boolean,
    numberOfArrayItemsToGenerate: number,
): PayloadObject[] {
    return Array.from({ length: numberOfArrayItemsToGenerate }, () =>
        generatePayload(schema, isPlaceholder),
    );
}

function generateArrayOfPrimitives(
    itemsShape: JsonSchema,
    isPlaceholder: boolean,
    numberOfArrayItemsToGenerate: number,
): PayloadPrimitive[] {
    const generatedItems: PayloadPrimitive[] = Array.from(
        { length: numberOfArrayItemsToGenerate },
        () => generateValue(itemsShape, isPlaceholder),
    ) as PayloadPrimitive[];

    return generatedItems.filter(
        (item: PayloadPrimitive) => item !== null && item.toString().length,
    );
}

/**
 * Generates array values (primitives or objects)
 */
function generateArray(
    schema: JsonSchema,
    isPlaceholder: boolean,
): PayloadPrimitive[] | PayloadObject[] {
    const { items: itemsSchema } = schema;

    if (itemsSchema === undefined) {
        return [];
    }

    const numberOfArrayItemsToGenerate = isPlaceholder
        ? PAYLOAD_GENERATOR_CONFIG.MIN_ARRAY_ITEMS
        : PAYLOAD_GENERATOR_CONFIG.MIN_ARRAY_ITEMS +
          Math.floor(Math.random() * PAYLOAD_GENERATOR_CONFIG.MAX_ARRAY_ITEMS);

    if (itemsSchema.properties !== undefined) {
        return generateArrayOfObjects(
            itemsSchema,
            isPlaceholder,
            numberOfArrayItemsToGenerate,
        );
    }

    return generateArrayOfPrimitives(
        itemsSchema,
        isPlaceholder,
        numberOfArrayItemsToGenerate,
    );
}

/**
 * Generates complete payload object from schema
 */
function generatePayload(
    schema: JsonSchema,
    isPlaceholder: boolean = false,
): PayloadObject {
    const payload: PayloadObject = {};

    if (schema.properties) {
        for (const key in schema.properties) {
            const property = schema.properties[key];

            payload[key] = generateValue(property, isPlaceholder);
        }
    }

    return payload;
}

/**
 * Generates a string value based on schema definition
 */
function generateString(schema: JsonSchema, isPlaceholder: boolean): string {
    if (isPlaceholder) {
        return '<placeholder>';
    }

    // Randomly return empty value for non-required fields
    if (
        !schema['x-required'] &&
        Math.random() < PAYLOAD_GENERATOR_CONFIG.EMPTY_FIELD_PROBABILITY
    ) {
        return '';
    }

    const range: { minLength: number; maxLength: number } = {
        ...PAYLOAD_GENERATOR_CONFIG.STRING_LENGTH,
    };

    if (schema.minLength) {
        range.minLength = schema.minLength;
    }

    if (schema.maxLength) {
        range.maxLength = schema.maxLength;
    }

    const generatedValue = generateFromMatchingGenerator(schema, range);

    if (generatedValue !== null) {
        return !(typeof generatedValue === 'string')
            ? generatedValue.toString()
            : generatedValue;
    }

    // Fallback to random alpha string
    return faker.string.alpha({
        length: {
            min: range.minLength,
            max: range.maxLength,
        },
    });
}

/**
 * Generates a string value based on schema definition
 */
function generateInteger(schema: JsonSchema, isPlaceholder: boolean): number {
    if (isPlaceholder) {
        return 0;
    }

    const range: { min: number; max: number } = {
        ...PAYLOAD_GENERATOR_CONFIG.NUMBER_RANGE,
    };

    if (schema.minimum) {
        range.min = schema.minimum;
    }

    if (schema.maximum) {
        range.max = schema.maximum;
    }

    const generatedValue = generateFromMatchingGenerator(schema, range, true);

    if (generatedValue !== null && typeof generatedValue === 'number') {
        return generatedValue;
    }

    // Fallback to random number
    return faker.number.int(range);
}

/**
 * Format to generator ID mapping.
 */
const FORMAT_TO_GENERATOR: Record<string, string> = {
    uuid: 'uuid',
    email: 'email',
    'date-time': 'datetime',
    url: 'url',
    date: 'date',
    time: 'time',
    uri: 'url',
};

/**
 * Gets the appropriate generator for a schema property
 */
function generateFromMatchingGenerator(
    schema: JsonSchema,
    range: { min?: number; max?: number; minLength?: number; maxLength?: number } = {},
    shouldBeInteger: boolean = false,
): string | number | bigint | null {
    if (schema.format && FORMAT_TO_GENERATOR[schema.format]) {
        const generatorId = FORMAT_TO_GENERATOR[schema.format];

        // Prioritized the defined format if defined
        return (
            allValueGenerators
                .find((generator: ValueGenerator) => generator.id === generatorId)
                ?.generate() ?? null
        );
    }

    // Otherwise, try to guess a generator based on the property name.
    for (const patternSettings of PROPERTY_NAME_PATTERNS) {
        if (shouldBeInteger && !patternSettings.isInteger) {
            continue;
        }

        if (!patternSettings.pattern.test(schema['x-name'])) {
            continue;
        }

        const matchingGenerator = allValueGenerators.find(
            (generator: ValueGenerator) => generator.id === patternSettings.generatorId,
        );

        if (!matchingGenerator) {
            continue;
        }

        return (
            matchingGenerator.generate({
                ...patternSettings.generatorConfig,
                ...range,
            }) ?? null
        );
    }

    return null;
}

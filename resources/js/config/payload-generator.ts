/**
 * Configuration constants for payload data generation
 */
export const PAYLOAD_GENERATOR_CONFIG = {
    EMPTY_FIELD_PROBABILITY: 0.3,
    MAX_ARRAY_ITEMS: 5,
    MIN_ARRAY_ITEMS: 1,
    NUMBER_RANGE: { min: 1, max: 9999 },
    STRING_LENGTH: { minLength: 5, maxLength: 20 },
} as const;

import { JSONSchema7 } from 'json-schema';

/*
 * A proxy for JSON Schema types for a more domain specific naming.
 */

export interface JsonSchema extends JSONSchema7 {
    'x-required': boolean;
    'x-name': string;
    items?: JsonSchema;
    properties?: {
        [key: string]: JsonSchema;
    };
}

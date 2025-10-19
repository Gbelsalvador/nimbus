/*
 * This file is auto-generated.
 * Don't update it manually, otherwise, your changes will be lost.
 * To update the file run `php bin/intellisense`.
 *
 * Generated at: 2025-09-22T17:58:32+00:00.
 */

export enum GeneratorType {
    Uuid = 'UUID',
    Email = 'Email',
    String = 'String',
}

/**
 * Source global headers type for request configuration
 */
export type SourceGlobalHeaders = {
    header: string;
    type: 'raw' | 'generator';
    value: GeneratorType | string | number;
};

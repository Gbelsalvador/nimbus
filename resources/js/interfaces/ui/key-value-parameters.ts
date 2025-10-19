/**
 * The full parameter shape used internally with the UI details.
 */
export interface ExtendedParameter {
    id: number;
    type: 'text' | 'file'; // <- Make an Enum.
    key: string;
    value: string;
    enabled: boolean;
}

/**
 * A minimal shape used to communicate with external components.
 */
export interface ParametersExternalContract {
    type?: 'text' | 'file'; // <- Form Input type.
    key: string;
    value: string;
}

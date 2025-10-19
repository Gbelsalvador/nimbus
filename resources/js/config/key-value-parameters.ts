/**
 * Key-value parameters configuration
 *
 * This configuration defines timing settings for parameter manipulation
 * and synchronization to provide smooth user interactions.
 */
export const keyValueParametersConfig = {
    /**
     * Deletion confirmation timeout in milliseconds (1.2 seconds).
     *
     * Time window for users to cancel parameter deletion after clicking delete.
     * Provides safety mechanism to prevent accidental data loss.
     */
    DELETION_CONFIRMATION_TIMEOUT: 1200,

    /**
     * Sync debounce delay in milliseconds (300ms).
     *
     * Delay before synchronizing parameter changes back to the parent model.
     * Prevents excessive updates during rapid user input and improves performance.
     */
    SYNC_DEBOUNCE_DELAY: 300,
} satisfies KeyValueParametersConfig;

export interface KeyValueParametersConfig {
    DELETION_CONFIRMATION_TIMEOUT: number;
    SYNC_DEBOUNCE_DELAY: number;
}

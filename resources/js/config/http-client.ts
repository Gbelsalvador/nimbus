/**
 * HTTP client configuration
 *
 * This configuration defines core settings for HTTP client behavior.
 */
export const httpClientConfig = {
    /**
     * Request timeout in milliseconds (30 seconds).
     *
     * Maximum time to wait for a response before considering the request failed.
     * Prevents hanging requests and provides user feedback for slow endpoints.
     */
    TIMEOUT: 30000,

    /**
     * Minimum delay in milliseconds (200ms).
     *
     * Artificial delay added to simulate realistic response times to have better UX.
     * Developers would know a new request happened.
     */
    MINIMUM_DELAY: 200,
} satisfies HttpClientConfig;

export interface HttpClientConfig {
    TIMEOUT: number;
    MINIMUM_DELAY: number;
}

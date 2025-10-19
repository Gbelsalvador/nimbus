import { ParametersExternalContract } from '@/interfaces';

/**
 * Checks if a query parameter is valid for inclusion in URLs.
 */
export function isValidQueryParameter(parameter: ParametersExternalContract): boolean {
    return parameter.key.trim() !== '';
}

/**
 * Builds complete request URL with query parameters.
 *
 * Constructs the full URL by combining base URL, endpoint, and
 * enabled query parameters for the current request.
 */
export function buildRequestUrl(
    baseUrl: string,
    endpoint: string,
    queryParameters: ParametersExternalContract[],
): string {
    const url = new URL(`${baseUrl}/${endpoint}`);

    queryParameters.forEach(parameter => {
        if (!isValidQueryParameter(parameter)) {
            return;
        }

        url.searchParams.append(parameter.key, parameter.value);
    });

    return url.toString();
}

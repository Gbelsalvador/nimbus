import { AuthorizationContract } from '@/interfaces/auth/authorization';
import { AuthorizationType } from '@/interfaces/generated';
import { PendingRequest, RequestBodyTypeEnum, RequestHeader } from '@/interfaces/http';
import { buildRequestUrl } from './request-url-builder';

/**
 * Result of cURL command generation.
 */
export interface CurlGenerationResult {
    command: string;
    hasSpecialAuth: boolean;
}

/**
 * Generates a complete cURL command from a pending request.
 *
 * Builds a properly formatted cURL command with method, URL, headers,
 * authorization, and body data.
 */
export function generateCurlCommand(
    request: PendingRequest,
    baseUrl: string,
): CurlGenerationResult {
    const methodPart = buildHttpMethodPart(request.method);
    // TODO [Bug] Properly create the url when the method is GET (body becomes query params).
    const urlPart = buildRequestUrlPart(request, baseUrl);
    const headerParts = buildRequestHeaderParts(request);
    const authPart = buildAuthorizationHeaderPart(request.authorization);
    const bodyParts = getRequestBodyParts(request);

    const command = ['curl']
        .concat(methodPart ? [methodPart] : [])
        .concat([urlPart])
        .concat(headerParts)
        .concat(authPart ? [authPart] : [])
        .concat(bodyParts)
        .join(' \\\n  ');

    return {
        command: command,
        hasSpecialAuth: requiresSpecialAuthorization(request.authorization),
    };
}

/**
 * Builds HTTP method part.
 */
function buildHttpMethodPart(method: string): string | null {
    const upperMethod = method.toUpperCase();

    // GET is the default HTTP method in cURL, so no explicit flag needed
    if (upperMethod === 'GET') {
        return null;
    }

    return `-X ${upperMethod}`;
}

/**
 * Builds request URL part.
 */
function buildRequestUrlPart(request: PendingRequest, baseUrl: string): string {
    const fullUrl = buildRequestUrl(baseUrl, request.endpoint, request.queryParameters);

    return `"${fullUrl}"`;
}

/**
 * Builds request header parts.
 */
function buildRequestHeaderParts(request: PendingRequest): string[] {
    const validHeaders = getValidHeaders(request);

    const headerParts = validHeaders.map(header => `-H "${header.key}: ${header.value}"`);

    // Add Content-Type header for JSON payloads if not already present
    if (request.payloadType === RequestBodyTypeEnum.JSON) {
        const hasContentType = validHeaders.some(
            header => header.key.toLowerCase() === 'content-type',
        );

        if (!hasContentType) {
            headerParts.push(`-H "Content-Type: application/json"`);
        }
    }

    return headerParts;
}

/**
 * Builds authorization header part.
 */
function buildAuthorizationHeaderPart(
    authorization: AuthorizationContract,
): string | null {
    const authHeader = buildAuthHeader(authorization);

    if (!authHeader) {
        return null;
    }

    return `-H "${authHeader}"`;
}

/**
 * Builds request body parts.
 */
function getRequestBodyParts(request: PendingRequest): string[] {
    const bodyData = extractRequestBody(request);

    if (bodyData === null) {
        return [];
    }

    return bodyData;
}

/**
 * Filters headers to only include valid ones.
 */
function getValidHeaders(request: PendingRequest): RequestHeader[] {
    return request.headers.filter(isValidHeader);
}

/**
 * Checks if a header is valid for inclusion.
 */
function isValidHeader(header: RequestHeader): boolean {
    return header.key.trim() !== '' && header.value !== null && header.value !== '';
}

/**
 * Builds authorization header string.
 */
function buildAuthHeader(authorization: AuthorizationContract): string | null {
    if (!authorization) {
        return null;
    }

    switch (authorization.type) {
        case AuthorizationType.Bearer:
            return `Authorization: Bearer ${authorization.value}`;

        case AuthorizationType.Basic:
            return buildBasicAuthHeader(authorization.value);

        case AuthorizationType.None:
        case AuthorizationType.CurrentUser:
        case AuthorizationType.Impersonate:
            return null;

        default:
            return null;
    }
}

/**
 * Builds Basic authentication header.
 */
function buildBasicAuthHeader(authValue: {
    username: string;
    password: string;
}): string | null {
    // btoa() encodes username:password string to Base64 for HTTP Basic Authentication
    const credentials = btoa(`${authValue.username}:${authValue.password}`);

    return `Authorization: Basic ${credentials}`;
}

function extractRequestBody(request: PendingRequest): string[] | null {
    const bodyData = request.body;

    const methodBodies = bodyData[request.method];

    if (methodBodies === undefined) {
        return null;
    }

    const body = methodBodies[request.payloadType];

    if (body === undefined) {
        return null;
    }

    return formatBodyValue(body, request.payloadType);
}

/**
 * Formats body value based on payload type.
 */
function formatBodyValue(
    bodyValue: string | FormData | null,
    payloadType: RequestBodyTypeEnum,
): string[] | null {
    switch (payloadType) {
        case RequestBodyTypeEnum.JSON:
            return typeof bodyValue === 'string' ? [bodyValue] : null;

        case RequestBodyTypeEnum.FORM_DATA:
            return formatFormDataValue(bodyValue);

        case RequestBodyTypeEnum.PLAIN_TEXT:
            return typeof bodyValue === 'string' ? [bodyValue] : null;

        case RequestBodyTypeEnum.EMPTY:
            return null;

        default:
            return null;
    }
}

/**
 * Formats form data value for cURL command.
 */
function formatFormDataValue(bodyValue: string | FormData | null): string[] | null {
    if (bodyValue instanceof FormData) {
        return convertFormDataToCUrlFields(bodyValue);
    }

    return null;
}

/**
 * Converts FormData object to cURL field strings.
 */
function convertFormDataToCUrlFields(formData: FormData): string[] {
    return Array.from(formData.entries()).map(([key, value]) => {
        if (value instanceof File) {
            return `${key}=@${value.name}`;
        }

        return `${key}=${value}`;
    });
}

/**
 * Checks if authorization requires special handling (not standard HTTP headers).
 */
function requiresSpecialAuthorization(authorization: AuthorizationContract): boolean {
    if (authorization.type === AuthorizationType.CurrentUser) {
        return true;
    }

    return authorization.type === AuthorizationType.Impersonate;
}

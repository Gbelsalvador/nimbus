export interface CookieValue {
    raw: string;
    decrypted: string | null;
}

export interface ResponseCookie {
    key: string;
    value: CookieValue;
    domain?: string;
    path?: string;
    expires?: string;
    httpOnly?: boolean;
    secure?: boolean;
}

<?php

namespace Sunchayn\Nimbus\Modules\Relay\Authorization;

readonly class AuthorizationCredentials
{
    /**
     * @param  string|array{username: string, password: string}|null  $value
     */
    public function __construct(
        public AuthorizationTypeEnum $type,
        public string|array|null $value,
    ) {}

    public static function none(): self
    {
        return new self(
            type: AuthorizationTypeEnum::None,
            value: null,
        );
    }
}

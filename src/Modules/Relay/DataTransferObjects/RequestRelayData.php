<?php

namespace Sunchayn\Nimbus\Modules\Relay\DataTransferObjects;

use Illuminate\Support\Collection;
use Sunchayn\Nimbus\Http\Api\Relay\NimbusRelayRequest;
use Sunchayn\Nimbus\Modules\Relay\Authorization\AuthorizationCredentials;
use Sunchayn\Nimbus\Modules\Relay\Authorization\AuthorizationTypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag;

readonly class RequestRelayData
{
    /**
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $body
     */
    public function __construct(
        public string $method,
        public string $endpoint,
        public AuthorizationCredentials $authorization,
        public array $headers,
        public array $body,
        public ParameterBag $cookies,
    ) {}

    public static function fromRelayApiRequest(NimbusRelayRequest $nimbusRelayRequest): self
    {
        /**
         * @var array{
         *     headers?: array<array-key, array{key: string, value: string}>,
         *     method: string,
         *     endpoint: string,
         *     authorization?: array{type: string, value?: string|array{username: string, password: string}|null},
         *     body: mixed,
         * } $data
         **/
        $data = $nimbusRelayRequest->validated();

        /** @var Collection<string, string> $headers */
        $headers = collect($data['headers'] ?? [])
            ->pluck('value', 'key');

        $headers->when(
            ! $headers->has('Accept'),
            fn () => $headers->put('Accept', 'application/json'),
        );

        $headers->when(
            $nimbusRelayRequest->userAgent() !== null,
            fn () => $headers->put('User-Agent', (string) $nimbusRelayRequest->userAgent()),
        );

        return new self(
            method: strtolower($data['method']),
            endpoint: $data['endpoint'],
            authorization: array_key_exists('authorization', $data)
                ? new AuthorizationCredentials(
                    type: AuthorizationTypeEnum::from($data['authorization']['type']),
                    value: $data['authorization']['value'] ?? null,
                )
                : AuthorizationCredentials::none(),
            headers: $headers->mapWithKeys(fn (mixed $value, string $key): array => [strtolower($key) => $value])->all(),
            body: $nimbusRelayRequest->getBody(),
            cookies: $nimbusRelayRequest->cookies,
        );
    }
}

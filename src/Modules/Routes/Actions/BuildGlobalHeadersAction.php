<?php

namespace Sunchayn\Nimbus\Modules\Routes\Actions;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Support\Arr;
use Sunchayn\Nimbus\Modules\Config\GlobalHeaderGeneratorTypeEnum;

class BuildGlobalHeadersAction
{
    public function __construct(
        private readonly ConfigRepository $configRepository,
    ) {}

    /**
     * @return array<array-key, scalar|null>
     */
    public function execute(): array
    {
        /** @var array<array-key, mixed> $headers */
        $headers = $this->configRepository->get('nimbus.headers');

        return array_values(
            Arr::map(
                $headers,
                fn (mixed $value, string $header): array => [
                    'header' => $header,
                    'type' => $value instanceof GlobalHeaderGeneratorTypeEnum ? 'generator' : 'raw',
                    'value' => match (true) {
                        $value instanceof GlobalHeaderGeneratorTypeEnum => $value->value,
                        is_scalar($value) => $value,
                        default => null,
                    },
                ],
            ),
        );
    }
}

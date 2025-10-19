<?php

namespace Sunchayn\Nimbus\Modules\Routes\Services\Uri;

class NonVersionedUri implements UriContract
{
    /** @var string[] */
    private array $parts;

    public function __construct(
        public string $value,
        public string $routesPrefix,
    ) {
        $this->parts = explode('/', $value);
    }

    public function getVersion(): string
    {
        return 'n/a';
    }

    public function getResource(): string
    {
        // Remove empty parts from the URI and reindex array
        $cleanParts = array_values(array_filter($this->parts, fn (string $part): bool => $part !== ''));

        $cleanParts = $this->removePrefixIfPresent($cleanParts);

        // Extract the resource (first remaining part)
        return array_shift($cleanParts) ?? '';
    }

    /**
     * @param  string[]  $parts
     * @return string[]
     */
    private function removePrefixIfPresent(array $parts): array
    {
        if (! filled($this->routesPrefix)) {
            return $parts;
        }

        if ($parts !== [] && $parts[0] === $this->routesPrefix) {
            array_shift($parts);
        }

        return $parts;
    }
}

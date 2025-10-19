<?php

namespace Sunchayn\Nimbus\Modules\Routes\Services\Uri;

class VersionedUri implements UriContract
{
    /** @var string[] */
    private array $cleanParts;

    public function __construct(
        public string $value,
        public string $routesPrefix,
    ) {
        $this->cleanParts = $this->parseAndCleanUri();
    }

    public function getVersion(): string
    {
        if ($this->cleanParts !== [] && $this->isVersionPart($this->cleanParts[0])) {
            return $this->cleanParts[0];
        }

        return 'v1';
    }

    public function getResource(): string
    {
        // If there's a version part, skip it to get the resource
        if ($this->cleanParts !== [] && $this->isVersionPart($this->cleanParts[0])) {
            return $this->cleanParts[1] ?? '';
        }

        // If there's no version part, the first part is the resource
        return $this->cleanParts[0] ?? '';
    }

    /**
     * @return string[]
     */
    private function parseAndCleanUri(): array
    {
        $parts = explode('/', $this->value);

        // Remove empty parts from the URI and reindex array
        $cleanParts = array_values(array_filter($parts, fn ($part): bool => $part !== ''));

        // Remove the routes prefix if present
        if ($this->routesPrefix !== '' && $cleanParts !== [] && $cleanParts[0] === $this->routesPrefix) {
            array_shift($cleanParts);
        }

        return $cleanParts;
    }

    private function isVersionPart(string $part): bool
    {
        // Check if the part looks like a version (e.g., v1, v2, 1.0, etc.)
        return preg_match('/^v\d+$/', $part) || preg_match('/^\d+\.\d+$/', $part);
    }
}

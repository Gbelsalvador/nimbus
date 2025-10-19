<?php

namespace Sunchayn\Nimbus\Modules\Routes\DataTransferObjects;

use Sunchayn\Nimbus\Modules\Routes\ValueObjects\Endpoint;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;

/**
 * @codeCoverageIgnore a DTO with no behavior.
 */
class ExtractedRoute
{
    /**
     * @param  string[]  $methods
     */
    public function __construct(
        public readonly Endpoint $uri,
        public readonly array $methods,
        public readonly Schema $schema,
    ) {}
}

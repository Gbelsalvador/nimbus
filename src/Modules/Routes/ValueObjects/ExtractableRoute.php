<?php

namespace Sunchayn\Nimbus\Modules\Routes\ValueObjects;

use Closure;
use PhpParser\Node;
use ReflectionParameter;

class ExtractableRoute
{
    /**
     * @param  ReflectionParameter[]  $parameters
     * @param  Closure() : (Node[]|null)  $codeParser
     */
    public function __construct(
        public readonly array $parameters,
        public readonly Closure $codeParser,
        public readonly ?string $methodName = null,
        public readonly ?string $controllerClass = null,
        public readonly ?string $controllerMethod = null,
    ) {}

    public static function empty(): self
    {
        return new self(
            parameters: [],
            codeParser: fn (): array => [],
        );
    }
}

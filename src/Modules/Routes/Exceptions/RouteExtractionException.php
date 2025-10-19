<?php

namespace Sunchayn\Nimbus\Modules\Routes\Exceptions;

use RuntimeException;
use Throwable;

abstract class RouteExtractionException extends RuntimeException
{
    private readonly string $controllerClass;

    private readonly string $controllerMethod;

    /**
     * @param  string[]  $routeMethods
     */
    public function __construct(
        string $message,
        private readonly ?string $routeUri = null,
        private readonly ?array $routeMethods = null,
        ?string $controllerClass = null,
        ?string $controllerMethod = null,
        private readonly ?string $suggestedSolution = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $this->controllerClass = filled($controllerClass) ? $controllerClass : '[unspecified]';

        $this->controllerMethod = filled($controllerMethod) ? $controllerMethod : '[unspecified]';

        parent::__construct($message, $code, $previous);
    }

    public function getSuggestedSolution(): ?string
    {
        return $this->suggestedSolution;
    }

    public function getIgnoreData(): ?string
    {
        if ($this->routeUri === null || $this->routeMethods === null) {
            return null;
        }

        return implode('|', [
            $this->routeUri,
            json_encode($this->routeMethods),
        ]);
    }

    /**
     * @return array<string, array<string>|string|null>
     */
    public function getRouteContext(): array
    {
        return [
            'uri' => $this->routeUri,
            'methods' => $this->routeMethods,
            'controllerClass' => $this->controllerClass,
            'controllerMethod' => $this->controllerMethod,
        ];
    }
}

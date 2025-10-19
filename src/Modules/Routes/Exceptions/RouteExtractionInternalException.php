<?php

namespace Sunchayn\Nimbus\Modules\Routes\Exceptions;

use Throwable;

class RouteExtractionInternalException extends RouteExtractionException
{
    /**
     * @param  string[]  $routeMethods
     */
    public static function forRoute(
        Throwable $throwable,
        string $routeUri,
        array $routeMethods,
        ?string $controllerClass = null,
        ?string $controllerMethod = null,
    ): self {
        return new self(
            message: sprintf("Failed to extract route information for '%s' due to an unexpected error: %s", $routeUri, $throwable->getMessage()),
            routeUri: $routeUri,
            routeMethods: $routeMethods,
            controllerClass: $controllerClass,
            controllerMethod: $controllerMethod,
            suggestedSolution: 'Check the application logs for more details and ensure all dependencies are properly installed.'
                .'<br />In case of internal errors, please open an issue: <a class="hover:underline" href="https://github.com/sunchayn/nimbus/issues/new/choose">https://github.com/sunchayn/nimbus/issues/new/choose</a>',
            code: $throwable->getCode(),
            previous: $throwable,
        );
    }
}

<?php

namespace Sunchayn\Nimbus\Modules\Routes\Exceptions;

class InvalidRouteDefinitionException extends RouteExtractionException
{
    /**
     * @param  string[]  $routeMethods
     */
    public static function forRoute(
        string $routeUri,
        array $routeMethods,
        string $controllerClass,
        string $controllerMethod,
    ): self {
        $properlyFormattedUsesStatement = filled($controllerClass) && filled($controllerMethod);

        $message = $properlyFormattedUsesStatement
            ? sprintf("Controller method '%s' not found in class '%s' for route '%s'.", $controllerMethod, $controllerClass, $routeUri)
            : sprintf("Malformed `uses` statement for route '%s'.", $routeUri);

        $suggestedSolution = $properlyFormattedUsesStatement
            ? sprintf("Check that the method '%s' exists in the '%s' class. This usually indicates an incorrect route definition in your routes file.", $controllerMethod, $controllerClass)
            : 'Make sure the `uses` statement is properly formatted `{controllerClass}@{controllerMethod}`. If it is an invokable controller then it must not have the `@` suffix.';

        return new self(
            message: $message,
            routeUri: $routeUri,
            routeMethods: $routeMethods,
            controllerClass: $controllerClass,
            controllerMethod: $controllerMethod,
            suggestedSolution: $suggestedSolution,
        );
    }
}

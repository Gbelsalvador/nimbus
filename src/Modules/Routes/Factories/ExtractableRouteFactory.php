<?php

namespace Sunchayn\Nimbus\Modules\Routes\Factories;

use Closure;
use Illuminate\Routing\Route;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Sunchayn\Nimbus\Modules\Routes\Exceptions\InvalidRouteDefinitionException;
use Sunchayn\Nimbus\Modules\Routes\Exceptions\RouteExtractionException;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\ExtractableRoute;

/**
 * Creates ExtractableRoute objects from Laravel Route instances.
 *
 * Handles the complexity of parsing `controller@method` syntax, validating
 * controller existence, and preparing code analysis tools for validation extraction.
 */
class ExtractableRouteFactory
{
    private static ?ParserFactory $parserFactory = null;

    /**
     * Converts a Laravel route into an ExtractableRoute for validation analysis.
     *
     * @throws RouteExtractionException
     */
    public function fromLaravelRoute(Route $route): ExtractableRoute
    {
        $uses = $route->getAction('uses');

        // Currently only supports controller@method routes. Closure-based routes
        // would require different extraction strategies and are not yet supported
        if (! is_string($uses) || $this->isSerializedRoute($uses)) {
            return ExtractableRoute::empty();
        }

        $parts = explode('@', $uses);

        $controllerClassName = $parts[0];
        $controllerMethod = $parts[1] ?? '';

        if ($controllerClassName === '' || $controllerClassName === '0' || ($controllerMethod === '' || $controllerMethod === '0')) {
            throw InvalidRouteDefinitionException::forRoute(
                routeUri: $route->uri(),
                routeMethods: $route->methods(),
                controllerClass: $controllerClassName,
                controllerMethod: $controllerMethod,
            );
        }

        /** @var class-string $controllerClassName */
        $methodInfo = $this->getMethodInfo($controllerClassName, $controllerMethod);

        if ($methodInfo === null) {
            throw InvalidRouteDefinitionException::forRoute(
                routeUri: $route->uri(),
                routeMethods: $route->methods(),
                controllerClass: $controllerClassName,
                controllerMethod: $controllerMethod,
            );
        }

        return new ExtractableRoute(
            parameters: $methodInfo['parameters'],
            codeParser: $this->getCodeParserFor($methodInfo['fileName']),
            methodName: $controllerMethod,
            controllerClass: $controllerClassName,
            controllerMethod: $controllerMethod,
        );
    }

    /**
     * Validates controller and method existence, returning metadata for extraction.
     *
     * Returns null if the controller class or method doesn't exist, allowing
     * the factory to handle missing controllers gracefully with proper exceptions.
     *
     * @param  class-string  $class
     * @param  non-empty-string  $method
     * @return null|array{
     *     parameters: array<int, ReflectionParameter>,
     *     fileName: non-empty-string
     * }
     */
    private function getMethodInfo(string $class, string $method): ?array
    {
        if (! class_exists($class)) {
            return null;
        }

        if (! method_exists($class, $method)) {
            return null;
        }

        $fileName = (new ReflectionClass($class))->getFileName();

        if ($fileName === false) {
            return null;
        }

        $parameters = $this->getMethodParameters($class, $method);

        return [
            'parameters' => $parameters,
            'fileName' => $fileName,
        ];
    }

    private function isSerializedRoute(string $value): bool
    {
        return str_starts_with($value, 'a:') || str_starts_with($value, 'O:');
    }

    /**
     * Extracts method parameters for validation analysis.
     *
     * Method parameters are needed to identify FormRequest classes that
     * contain validation rules for extraction.
     *
     * @param  class-string  $class
     * @param  non-empty-string  $method
     * @return array<int, ReflectionParameter>
     */
    private function getMethodParameters(string $class, string $method): array
    {
        try {
            $reflectionClass = new ReflectionClass($class);
            $reflectionMethod = $reflectionClass->getMethod($method);
        } catch (ReflectionException) {
            return [];
        }

        return $reflectionMethod->getParameters();
    }

    /**
     * Creates a parser closure for the controller file.
     *
     * Uses rescue() to prevent parsing errors from crashing the application
     * when controller files contain syntax errors or are unreadable.
     *
     * @param  non-empty-string  $fileName
     * @return Closure(): (Node[]|null)
     */
    private function getCodeParserFor(string $fileName): Closure
    {
        return fn (): ?array => rescue(
            fn (): ?array => $this->getParser()->parse(file_get_contents($fileName) ?: ''),
            report: false,
        );
    }

    /**
     * Provides a singleton PHP parser instance.
     *
     * ParserFactory is expensive to create, so we reuse a single instance
     * across all route extractions for better performance.
     */
    private function getParser(): Parser
    {
        if (! self::$parserFactory instanceof \PhpParser\ParserFactory) {
            self::$parserFactory = new ParserFactory;
        }

        return self::$parserFactory->createForNewestSupportedVersion();
    }
}

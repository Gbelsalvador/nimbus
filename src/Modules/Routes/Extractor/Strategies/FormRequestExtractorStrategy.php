<?php

namespace Sunchayn\Nimbus\Modules\Routes\Extractor\Strategies;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\RulesMethodVisitor;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\ExtractableRoute;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\RulesExtractionError;
use Sunchayn\Nimbus\Modules\Schemas\Builders\SchemaBuilder;
use Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;
use Throwable;

/**
 * Extracts validation rules from Laravel Form Request classes.
 *
 * @example
 * Controller: public function store(StoreUserRequest $request)
 * FormRequest: rules() returns ['name' => 'required|string']
 * Output: Schema with name field as required string
 */
class FormRequestExtractorStrategy implements ExtractorStrategyContract
{
    public function __construct(
        private readonly SchemaBuilder $schemaBuilder,
    ) {}

    public function matches(ExtractableRoute $extractableRoute): bool
    {
        foreach ($extractableRoute->parameters as $parameter) {
            if (! $parameter->hasType()) {
                continue;
            }

            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType) {
                continue;
            }

            $parameterType = $type->getName();

            // If Laravel Request is used,
            // we cannot figure out the schema from the request.
            if ($parameterType === Request::class) {
                return false;
            }

            // If it is not a form request instance, we continue.
            if (! is_subclass_of($parameterType, Request::class)) {
                continue;
            }

            return true;
        }

        return false; // <- didn't find a form request.
    }

    public function extract(ExtractableRoute $extractableRoute): Schema
    {
        $requestParameter = Arr::first(
            $extractableRoute->parameters,
            function (ReflectionParameter $reflectionParameter): bool {
                $type = $reflectionParameter->getType();

                if (! $type instanceof ReflectionNamedType) {
                    return false;
                }

                return is_subclass_of($type->getName(), Request::class);
            },
        );

        if (! $requestParameter) {
            return Schema::empty();
        }

        $type = $requestParameter->getType();

        if (! $type instanceof ReflectionNamedType) {
            return Schema::empty();
        }

        /** @var class-string $requestClassName */
        $requestClassName = $type->getName();

        $instance = new $requestClassName;

        if (! method_exists($instance, 'rules')) {
            return Schema::empty();
        }

        try {
            $rules = Ruleset::fromLaravelRules($instance->rules());
        } catch (Throwable $throwable) {
            // We will give it one extra attempt to figure out the shape statically as much as possible.
            $rules = $this->attemptGettingRulesShape($requestClassName);

            $throwable = new RulesExtractionError(
                throwable: $throwable,
            );
        }

        return $this->schemaBuilder->buildSchemaFromRuleset($rules, rulesExtractionError: $throwable ?? null);
    }

    /**
     * In some situations, the rules method might break due to dependency on request context,
     * or any other information that is not available when calling it statically.
     *
     * @param  class-string  $requestClassName
     */
    private function attemptGettingRulesShape(string $requestClassName): Ruleset
    {
        if (! method_exists($requestClassName, 'rules')) {
            return Ruleset::empty();
        }

        try {
            $fileName = (new ReflectionClass($requestClassName))->getFileName();
        } catch (ReflectionException) {
            return Ruleset::empty();
        }

        if (! $fileName || ! file_exists($fileName)) {
            return Ruleset::empty();
        }

        $parser = (new ParserFactory)->createForNewestSupportedVersion();

        $ast = $parser->parse(file_get_contents($fileName) ?: '');

        if ($ast === null) {
            return Ruleset::empty();
        }

        return $this->getRulesFromRequestAst($ast);
    }

    /**
     * @param  Node[]  $ast
     */
    private function getRulesFromRequestAst(array $ast): Ruleset
    {
        $rulesMethodVisitor = new RulesMethodVisitor;

        $nodeTraverser = new NodeTraverser;

        $nodeTraverser->addVisitor($rulesMethodVisitor);
        $nodeTraverser->traverse($ast);

        return $rulesMethodVisitor->getRules();
    }
}

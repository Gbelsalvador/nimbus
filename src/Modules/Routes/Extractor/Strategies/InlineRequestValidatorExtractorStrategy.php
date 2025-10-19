<?php

namespace Sunchayn\Nimbus\Modules\Routes\Extractor\Strategies;

use PhpParser\NodeTraverser;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\ValidateCallVisitor;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\ExtractableRoute;
use Sunchayn\Nimbus\Modules\Schemas\Builders\SchemaBuilder;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;

class InlineRequestValidatorExtractorStrategy implements ExtractorStrategyContract
{
    public function __construct(
        private readonly SchemaBuilder $schemaBuilder,
    ) {}

    public function matches(ExtractableRoute $extractableRoute): bool
    {
        // This strategy doesn't support anonymous routes for now.
        return $extractableRoute->methodName !== null;
    }

    public function extract(ExtractableRoute $extractableRoute): Schema
    {
        if (! $this->matches($extractableRoute)) {
            return Schema::empty();
        }

        if ($extractableRoute->methodName === null) {
            return Schema::empty();
        }

        $ast = ($extractableRoute->codeParser)();

        if ($ast === null) {
            return Schema::empty();
        }

        $validateCallVisitor = new ValidateCallVisitor($extractableRoute->methodName);

        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor($validateCallVisitor);
        $nodeTraverser->traverse($ast);

        return $this->schemaBuilder->buildSchemaFromRuleset($validateCallVisitor->getRules());
    }
}

<?php

namespace Sunchayn\Nimbus\Modules\Routes\Extractor\Ast;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\Shared\QualifiesTypehint;
use Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset;

/**
 * Extracts validation rules from FormRequest's `rules()` method.
 *
 * Walks through a class AST to find the `rules()` method and extracts
 * its return statement to determine the validation rules array.
 */
class RulesMethodVisitor extends NodeVisitorAbstract
{
    use QualifiesTypehint;

    private ?Ruleset $rules = null;

    /** @var array<string, mixed> Variables defined within the rules() method */
    private array $variablesContext = [];

    public function beforeTraverse(array $nodes): array
    {
        return $this->qualifyClassTypeHinting($nodes);
    }

    public function getRules(): Ruleset
    {
        return $this->rules ?? Ruleset::fromLaravelRules([]);
    }

    public function enterNode(Node $node): null|int|Node|array
    {
        if (! $this->isRulesMethod($node)) {
            return null;
        }

        /** @var Node\Stmt\ClassMethod $node */
        $this->extractRulesFromMethod($node);

        return NodeVisitor::STOP_TRAVERSAL;
    }

    private function isRulesMethod(Node $node): bool
    {
        return $node instanceof Node\Stmt\ClassMethod
            && $node->name->toString() === 'rules';
    }

    private function extractRulesFromMethod(Node\Stmt\ClassMethod $classMethod): void
    {
        if ($classMethod->stmts === null) {
            return;
        }

        foreach ($classMethod->stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\Return_) {
                $this->processReturnStatement($stmt);

                continue;
            }

            if ($stmt instanceof Node\Stmt\Expression && $stmt->expr instanceof Node\Expr\Assign) {
                $this->addVariableValueToContext($stmt->expr);
            }
        }
    }

    private function processReturnStatement(Node\Stmt\Return_ $return): void
    {
        if (! $return->expr instanceof \PhpParser\Node\Expr) {
            return;
        }

        $rules = ConvertNodeToConcreteValue::process($return->expr, $this->variablesContext);

        if (is_array($rules)) {
            $this->rules = Ruleset::fromLaravelRules($rules);
        }
    }

    private function addVariableValueToContext(Node\Expr\Assign $assign): void
    {
        if (! ($assign->var instanceof Node\Expr\Variable)) {
            return;
        }

        if (! is_string($assign->var->name)) {
            return;
        }

        $this->variablesContext[$assign->var->name] = ConvertNodeToConcreteValue::process($assign->expr, $this->variablesContext);
    }
}

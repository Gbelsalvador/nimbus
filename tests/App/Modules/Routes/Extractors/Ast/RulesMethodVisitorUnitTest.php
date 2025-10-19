<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Ast;

use Generator;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\ConvertNodeToConcreteValue;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\RulesMethodVisitor;
use Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset;

#[CoversClass(RulesMethodVisitor::class)]
#[CoversClass(ConvertNodeToConcreteValue::class)]
class RulesMethodVisitorUnitTest extends TestCase
{
    #[DataProvider('scenariosDataProvider')]
    public function test_it_works(
        string $phpCode,
        array $expectedRules,
    ): void {
        // Arrange

        // Parse the stub into AST
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $ast = $parser->parse($phpCode);

        $visitor = new RulesMethodVisitor;

        $traverser = new NodeTraverser;

        $traverser->addVisitor($visitor);

        // Act

        $traverser->traverse($ast);

        // Assert

        $this->assertEquals(Ruleset::fromLaravelRules($expectedRules), $visitor->getRules());
    }

    public static function scenariosDataProvider(): Generator
    {
        yield 'simple call' => [
            'phpCode' => file_get_contents(__DIR__.'/Stubs/FormRequestStub.php'),
            'expectedRules' => [
                'name' => 'required|string',
                'email' => 'required|email',
            ],
        ];

        yield 'with variables call' => [
            'phpCode' => file_get_contents(__DIR__.'/Stubs/FormRequestWithVariablesStub.php'),
            'expectedRules' => [
                'name' => 'required|string',
                'email' => 'required|string|email',
            ],
        ];

        yield 'with input conditional call' => [
            'phpCode' => file_get_contents(__DIR__.'/Stubs/FormRequestWithInputConditionalStub.php'),
            'expectedRules' => [
                'name' => null, // <- Unable cannot figure it out given it is conditional.
                'email' => 'required|string|email',
            ],
        ];
    }
}

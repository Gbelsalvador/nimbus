<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Ast;

use Generator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\ConvertNodeToConcreteValue;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Ast\ValidateCallVisitor;
use Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset;
use Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Stubs\StatusEnumStub;

#[CoversClass(ValidateCallVisitor::class)]
#[CoversClass(ConvertNodeToConcreteValue::class)]
class ValidateCallVisitorUnitTest extends TestCase
{
    #[DataProvider('scenariosDataProvider')]
    public function test_it_works(
        string $methodName,
        string $phpCode,
        array $expectedRules,
    ): void {
        // Arrange

        // Parse the stub into AST
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        $ast = $parser->parse($phpCode);

        $visitor = new ValidateCallVisitor($methodName);

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
            'methodName' => 'simple_call',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => 'required|string',
                'foobaz' => ['required', 'integer'],
            ],
        ];

        yield 'simple call without variable assignment' => [
            'methodName' => 'simple_call_without_assignment',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => 'required|string',
                'foobaz' => ['required', 'integer'],
            ],
        ];

        yield 'rules from sub method, concatenation, and interpolation' => [
            'methodName' => 'getting_rules_from_sub_method',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => 'required_with:foobaz',
                'foobarr' => 'present_with:foobar',
                'fooobazz' => 'required|string|email|max:200',
                'baz' => 'required|string|present|email',
                'foobaz' => ['required', 'string', 'email'],
            ],
        ];

        yield 'call from a variable' => [
            'methodName' => 'call_from_a_variable',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => 'required|string',
                'foobaz' => ['required', 'integer'],
            ],
        ];

        yield 'call from nested variables' => [
            'methodName' => 'call_from_nested_variables',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => 'required|string',
                'foobaz' => ['required', 'string', 'email'],
            ],
        ];

        yield 'call with rules instances' => [
            'methodName' => 'call_with_rules_instances',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => [
                    'required',
                    Rule::in(1, 2, 3, 4),
                    Rule::notIn(3, 4),
                    null, // <- RequiredIf cannot be resolved with a closure.
                    Rule::enum(StatusEnumStub::class),
                ],
                'foobaz' => ['required', 'string', new In(1, 2)],
            ],
        ];

        yield 'call with validateWithBag' => [
            'methodName' => 'call_validateWithBag',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [
                'foobar' => [
                    'required',
                    'in:1, 2, 3, 4',
                ],
                'foobaz' => ['required', 'string', 'email'],
            ],
        ];

        yield 'no validate call' => [
            'methodName' => 'no_validate_call',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [],
        ];

        yield 'calling validate on a non-request class' => [
            'methodName' => 'call_validate_on_different_class',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [],
        ];

        yield 'nested methods call' => [
            'methodName' => 'nested_methods_calls',
            'phpCode' => file_get_contents(__DIR__.'/Stubs/controller.stub.php'),
            'expectedRules' => [], // <- Currently this is not supported.
        ];
    }
}

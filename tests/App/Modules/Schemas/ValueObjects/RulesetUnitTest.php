<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Schemas\ValueObjects;

use Generator;
use Illuminate\Validation\Rule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset;

#[CoversClass(Ruleset::class)]
class RulesetUnitTest extends TestCase
{
    #[DataProvider('rulesetCreationDataProvider')]
    public function test_creates_ruleset_from_various_formats(array $input, array $expected): void
    {
        // Act

        $ruleset = Ruleset::fromLaravelRules($input);

        // Assert

        $this->assertEquals($expected, $ruleset->all());
    }

    public static function rulesetCreationDataProvider(): Generator
    {
        yield 'pipe-separated rules' => [
            'input' => ['example' => 'required|string|max:255'],
            'expected' => ['example' => ['required', 'string', 'max:255']],
        ];

        yield 'single rule' => [
            'input' => ['example' => 'required'],
            'expected' => ['example' => ['required']],
        ];

        yield 'empty string' => [
            'input' => ['example' => ''],
            'expected' => ['example' => []],
        ];

        yield 'array of rules' => [
            'input' => ['example' => ['required', 'string', 'max:255']],
            'expected' => ['example' => ['required', 'string', 'max:255']],
        ];

        yield 'empty array' => [
            'input' => [],
            'expected' => [],
        ];

        yield 'null input' => [
            'input' => ['example' => null],
            'expected' => ['example' => []],
        ];

        yield 'integer input' => [
            'input' => ['example' => 123],
            'expected' => ['example' => []],
        ];

        yield 'object input' => [
            'input' => ['example' => Rule::in(1, 2, 3)],
            'expected' => ['example' => [Rule::in(1, 2, 3)]],
        ];

        yield 'trailing pipe' => [
            'input' => ['example' => 'required|string|'],
            'expected' => ['example' => ['required', 'string']],
        ];

        yield 'leading pipe' => [
            'input' => ['example' => '|required|string'],
            'expected' => ['example' => ['required', 'string']],
        ];

        yield 'multiple consecutive pipes' => [
            'input' => ['example' => 'required||string'],
            'expected' => ['example' => ['required', 'string']],
        ];
    }

    #[DataProvider('emptyCheckDataProvider')]
    public function test_checks_if_ruleset_is_empty(array $rules, bool $expectedEmpty): void
    {
        // Arrange

        $ruleset = new Ruleset($rules);

        // Act

        $isEmpty = $ruleset->isEmpty();

        // Assert

        $this->assertEquals($expectedEmpty, $isEmpty);
    }

    public static function emptyCheckDataProvider(): Generator
    {
        yield 'Empty ruleset' => [
            'rules' => [],
            'expectedEmpty' => true,
        ];

        yield 'Non-empty ruleset' => [
            'rules' => ['example' => ['required', 'string']],
            'expectedEmpty' => false,
        ];

        yield 'Single rule' => [
            'rules' => ['example' => ['required']],
            'expectedEmpty' => false,
        ];
    }
}

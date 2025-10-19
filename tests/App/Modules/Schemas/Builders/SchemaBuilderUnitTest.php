<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Schemas\Builders;

namespace Sunchayn\Nimbus\Tests\App\Modules\Schemas\Builders;

use Generator;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sunchayn\Nimbus\Modules\Schemas\Builders\PropertyBuilder;
use Sunchayn\Nimbus\Modules\Schemas\Builders\SchemaBuilder;
use Sunchayn\Nimbus\Modules\Schemas\Collections\Ruleset;
use Sunchayn\Nimbus\Modules\Schemas\RulesMapper\Processors\EnumRuleProcessor;
use Sunchayn\Nimbus\Modules\Schemas\RulesMapper\Processors\InRuleProcessor;
use Sunchayn\Nimbus\Modules\Schemas\RulesMapper\RuleToSchemaMapper;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\FieldPath;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty;
use Sunchayn\Nimbus\Tests\App\Modules\Schemas\Builders\Stubs\StatusEnumStub;

#[CoversClass(SchemaBuilder::class)]
#[CoversClass(PropertyBuilder::class)]
#[CoversClass(FieldPath::class)]
#[CoversClass(RuleToSchemaMapper::class)]
#[CoversClass(InRuleProcessor::class)]
#[CoversClass(EnumRuleProcessor::class)]
// TODO [Test] Move the mapper and process to their own tests.
class SchemaBuilderUnitTest extends TestCase
{
    private SchemaBuilder $schemaBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaBuilder = $this->createSchemaBuilder();
    }

    #[DataProvider('schemaBuilderDataProvider')]
    public function test_builds_schema_with_correct_properties(
        array $rules,
        Schema $expectedSchema,
    ): void {
        // Act

        $actual = $this->schemaBuilder->buildSchemaFromRuleset(Ruleset::fromLaravelRules($rules));

        // Assert

        $this->assertEquals(
            $expectedSchema,
            $actual,
        );
    }

    public static function schemaBuilderDataProvider(): Generator
    {
        yield 'simple rules' => [
            'rules' => [
                'name' => 'required|string',
                'email' => 'required|email',
                'age' => 'required_with:email|integer|min:18|max:99',
                'statuses' => ['required', new Enum(StatusEnumStub::class)],
                'statuses_v2' => ['required', Rule::enum(StatusEnumStub::class)],
                'role' => ['required', new In(1, 2, 3, 4)],
                'role_v2' => ['required', Rule::in(1, 2, 3, 4)],
                'role_2' => ['required', new In(new stdClass, 2, 3)], // <- Assert we rebase the array after filtering.
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(name: 'name', type: 'string', required: true, format: null, itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'email', type: 'string', required: true, format: 'email', itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'age', type: 'integer', required: false, format: null, itemsSchema: null, propertiesSchema: null, minimum: 18, maximum: 99),
                    new SchemaProperty(name: 'statuses', type: 'string', required: true, format: null, enum: ['inactive', 'active'], itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'statuses_v2', type: 'string', required: true, format: null, enum: ['inactive', 'active'], itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'role', type: 'integer', required: true, format: null, enum: [1, 2, 3, 4], itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'role_v2', type: 'integer', required: true, format: null, enum: [1, 2, 3, 4], itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'role_2', type: 'integer', required: true, format: null, enum: [2, 3], itemsSchema: null, propertiesSchema: null),
                ],
                extractionError: null,
            ),
        ];

        yield 'nested object rules' => [
            'rules' => [
                'user.name' => 'required|string',
                'user.email' => 'required|email',
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(
                        name: 'user',
                        type: 'object',
                        required: false,
                        format: null,
                        itemsSchema: null,
                        propertiesSchema: new Schema(
                            properties: [
                                new SchemaProperty(name: 'name', type: 'string', required: true, format: null, itemsSchema: null, propertiesSchema: null),
                                new SchemaProperty(name: 'email', type: 'string', required: true, format: 'email', itemsSchema: null, propertiesSchema: null),
                            ],
                            extractionError: null,
                        ),
                    ),
                ],
                extractionError: null,
            ),
        ];

        yield 'array of primitives' => [
            'rules' => [
                'tags' => 'array',
                'tags.*' => 'string',
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(
                        name: 'tags',
                        type: 'array',
                        required: false,
                        format: null,
                        itemsSchema: new SchemaProperty(
                            name: 'tag', // <- Singular value of parent property `tags`.
                            type: 'string',
                            required: false,
                            format: null,
                            itemsSchema: null,
                            propertiesSchema: null,
                        ),
                        propertiesSchema: null,
                    ),
                ],
                extractionError: null,
            ),
        ];

        yield 'array of objects rules' => [
            'rules' => [
                'persons' => 'array|required',
                'persons.*.email' => 'string|email',
                'persons.*.username' => 'string|max:20',
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(
                        name: 'persons',
                        type: 'array',
                        required: true,
                        format: null,
                        itemsSchema: new SchemaProperty(
                            name: 'item',
                            type: 'object',
                            required: false,
                            format: null,
                            itemsSchema: null,
                            propertiesSchema: new Schema(
                                properties: [
                                    new SchemaProperty(
                                        name: 'email',
                                        type: 'string',
                                        required: false,
                                        format: 'email',
                                        itemsSchema: null,
                                        propertiesSchema: null,
                                    ),
                                    new SchemaProperty(
                                        name: 'username',
                                        type: 'string',
                                        required: false,
                                        format: null,
                                        itemsSchema: null,
                                        propertiesSchema: null,
                                        maximum: 20,
                                    ),
                                ],
                                extractionError: null,
                            )
                        ),
                        propertiesSchema: null,
                    ),
                ],
                extractionError: null,
            ),
        ];

        yield 'mixed data types' => [
            'rules' => [
                'id' => 'required|uuid',
                'name' => 'required|string',
                'age' => 'integer',
                'is_active' => 'boolean',
                'salary' => 'numeric',
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(name: 'id', type: 'string', required: true, format: 'uuid', itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'name', type: 'string', required: true, format: null, itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'age', type: 'integer', required: false, format: null, itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'is_active', type: 'boolean', required: false, format: null, itemsSchema: null, propertiesSchema: null),
                    new SchemaProperty(name: 'salary', type: 'number', required: false, format: null, itemsSchema: null, propertiesSchema: null),
                ],
                extractionError: null,
            ),
        ];

        yield 'empty rules' => [
            'rules' => [],
            'expectedSchema' => new Schema(
                properties: [],
                extractionError: null,
            ),
        ];

        yield 'deep nesting (object)' => [
            'rules' => [
                'company.department.team.member.name' => 'required|string',
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(
                        name: 'company',
                        type: 'object',
                        required: false,
                        format: null,
                        itemsSchema: null,
                        propertiesSchema: new Schema(
                            properties: [
                                new SchemaProperty(
                                    name: 'department',
                                    type: 'object',
                                    required: false,
                                    format: null,
                                    itemsSchema: null,
                                    propertiesSchema: new Schema(
                                        properties: [
                                            new SchemaProperty(
                                                name: 'team',
                                                type: 'object',
                                                required: false,
                                                format: null,
                                                itemsSchema: null,
                                                propertiesSchema: new Schema(
                                                    properties: [
                                                        new SchemaProperty(
                                                            name: 'member',
                                                            type: 'object',
                                                            required: false,
                                                            format: null,
                                                            itemsSchema: null,
                                                            propertiesSchema: new Schema(
                                                                properties: [
                                                                    new SchemaProperty(name: 'name', type: 'string', required: true, format: null, itemsSchema: null, propertiesSchema: null),
                                                                ],
                                                                extractionError: null,
                                                            ),
                                                        ),
                                                    ],
                                                    extractionError: null,
                                                ),
                                            ),
                                        ],
                                        extractionError: null,
                                    ),
                                ),
                            ],
                            extractionError: null,
                        ),
                    ),
                ],
                extractionError: null,
            ),
        ];
        yield 'deep nesting (array)' => [
            'rules' => [
                'company.teams.*.members.*.member.name' => 'string',
                'company.teams.*.members' => 'required|array',
            ],
            'expectedSchema' => new Schema(
                properties: [
                    new SchemaProperty(
                        name: 'company',
                        type: 'object',
                        required: false,
                        format: null,
                        enum: null,
                        itemsSchema: null,
                        propertiesSchema: new Schema(
                            properties: [
                                new SchemaProperty(
                                    name: 'teams',
                                    type: 'array',
                                    required: false,
                                    format: null,
                                    enum: null,
                                    itemsSchema: new SchemaProperty(
                                        name: 'item',
                                        type: 'object',
                                        required: false,
                                        format: null,
                                        enum: null,
                                        itemsSchema: null,
                                        propertiesSchema: new Schema(
                                            properties: [
                                                new SchemaProperty(
                                                    name: 'members',
                                                    type: 'array',
                                                    required: true,
                                                    format: null,
                                                    enum: null,
                                                    itemsSchema: new SchemaProperty(
                                                        name: 'item',
                                                        type: 'object',
                                                        required: false,
                                                        format: null,
                                                        enum: null,
                                                        itemsSchema: null,
                                                        propertiesSchema: new Schema(
                                                            properties: [
                                                                new SchemaProperty(
                                                                    name: 'member',
                                                                    type: 'object',
                                                                    required: false,
                                                                    format: null,
                                                                    enum: null,
                                                                    itemsSchema: null,
                                                                    propertiesSchema: new Schema(
                                                                        properties: [
                                                                            new SchemaProperty(
                                                                                name: 'name',
                                                                                type: 'string',
                                                                                required: false,
                                                                                format: null,
                                                                                enum: null,
                                                                                itemsSchema: null,
                                                                                propertiesSchema: null,
                                                                            ),
                                                                        ],
                                                                        extractionError: null,
                                                                    ),
                                                                ),
                                                            ],
                                                            extractionError: null,
                                                        ),
                                                    ),
                                                    propertiesSchema: null,
                                                ),
                                            ],
                                            extractionError: null,
                                        ),
                                    ),
                                    propertiesSchema: null,
                                ),
                            ],
                            extractionError: null,
                        ),
                    ),
                ],
                extractionError: null,
            ),
        ];
    }

    #[DataProvider('formatDetectionDataProvider')]
    public function test_detects_formats_correctly(
        array $rules,
        string $propertyName,
        ?string $expectedFormat
    ): void {
        // Act

        $schema = $this->schemaBuilder->buildSchemaFromRuleset(Ruleset::fromLaravelRules($rules));

        // Assert

        $property = $this->findPropertyByName($schema, $propertyName);

        $this->assertNotNull($property, "Property '{$propertyName}' not found");

        $this->assertEquals($expectedFormat, $property->format);
    }

    public static function formatDetectionDataProvider(): Generator
    {
        yield 'email format' => [
            'rules' => ['email' => 'required|email'],
            'propertyName' => 'email',
            'expectedFormat' => 'email',
        ];

        yield 'UUID format' => [
            'rules' => ['id' => 'required|uuid'],
            'propertyName' => 'id',
            'expectedFormat' => 'uuid',
        ];

        yield 'date-time format' => [
            'rules' => ['created_at' => 'required|date'],
            'propertyName' => 'created_at',
            'expectedFormat' => 'date-time',
        ];

        yield 'no format' => [
            'rules' => ['name' => 'required|string'],
            'propertyName' => 'name',
            'expectedFormat' => null,
        ];
    }

    #[DataProvider('enumValuesDataProvider')]
    public function test_extracts_enum_values_correctly(
        array $rules,
        string $propertyName,
        ?array $expectedEnum
    ): void {
        // Act

        $schema = $this->schemaBuilder->buildSchemaFromRuleset(Ruleset::fromLaravelRules($rules));

        // Assert

        $property = $this->findPropertyByName($schema, $propertyName);

        $this->assertNotNull($property, "Property '{$propertyName}' not found");

        $this->assertEquals($expectedEnum, $property->enum);
    }

    public static function enumValuesDataProvider(): Generator
    {
        yield 'enum values' => [
            'rules' => ['status' => 'required|in:active,inactive,pending'],
            'propertyName' => 'status',
            'expectedEnum' => ['active', 'inactive', 'pending'],
        ];

        yield 'no enum' => [
            'rules' => ['name' => 'required|string'],
            'propertyName' => 'name',
            'expectedEnum' => null,
        ];
    }

    /*
     * Helpers.
     */

    private function findPropertyByName(Schema $schema, string $name): ?SchemaProperty
    {
        return Arr::first(
            $schema->properties,
            fn (SchemaProperty $property) => $property->name === $name,
        );
    }

    /*
     * Generators.
     */

    private function createSchemaBuilder(): SchemaBuilder
    {
        $ruleMapper = new RuleToSchemaMapper;
        $propertyBuilder = new PropertyBuilder($ruleMapper);

        return new SchemaBuilder(
            $propertyBuilder,
        );
    }
}

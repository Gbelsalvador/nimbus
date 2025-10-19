<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Schemas\ValueObjects;

use Generator;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\RulesExtractionError;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty;

#[CoversClass(Schema::class)]
class SchemaUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_builds_empty_schema(): void
    {
        // Act

        $schema = Schema::empty();

        // Assert

        $this->assertEmpty($schema->properties);
        $this->assertNull($schema->extractionError);
    }

    #[DataProvider('isEmptyCheckDataProvider')]
    public function test_checks_if_empty(Schema $schema, bool $expected): void
    {
        // Act

        $isEmpty = $schema->isEmpty();

        // Assert

        $this->assertEquals($expected, $isEmpty);
    }

    public static function isEmptyCheckDataProvider(): Generator
    {
        yield 'Empty schema' => [
            'schema' => new Schema(properties: []),
            'expected' => true,
        ];

        yield 'Empty with extraction error' => [
            'schema' => new Schema(
                properties: [],
                extractionError: new RulesExtractionError(new RuntimeException)
            ),
            'expected' => true,
        ];

        yield 'Non-empty schema' => [
            'schema' => new Schema(properties: [new SchemaProperty(name: 'field')]),
            'expected' => false,
        ];
    }

    #[DataProvider('toArrayDataProvider')]
    public function test_converts_to_array(array $properties, array $expected): void
    {
        // Arrange

        $schema = new Schema(properties: $properties);

        // Act

        $result = $schema->toArray();

        // Assert

        $this->assertEquals($expected, $result);
    }

    public static function toArrayDataProvider(): Generator
    {
        yield 'empty schema' => [
            'properties' => [],
            'expected' => [],
        ];

        yield 'single property' => [
            'properties' => [
                new class(name: 'name') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string'];
                    }
                },
            ],
            'expected' => [
                'name' => ['type' => 'string'],
            ],
        ];

        yield 'multiple properties' => [
            'properties' => [
                new class(name: 'name') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string'];
                    }
                },
                new class(name: 'age') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'integer'];
                    }
                },
            ],
            'expected' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
        ];

        yield 'properties with formats' => [
            'properties' => [
                new class(name: 'email') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string', 'format' => 'email'];
                    }
                },
                new class(name: 'uuid') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string', 'format' => 'uuid'];
                    }
                },
            ],
            'expected' => [
                'email' => ['type' => 'string', 'format' => 'email'],
                'uuid' => ['type' => 'string', 'format' => 'uuid'],
            ],
        ];

        yield 'nested object properties' => [
            'properties' => [
                new class(name: 'user') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'email' => ['type' => 'string', 'format' => 'email'],
                            ],
                        ];
                    }
                },
            ],
            'expected' => [
                'user' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'email' => ['type' => 'string', 'format' => 'email'],
                    ],
                ],
            ],
        ];

        yield 'array properties' => [
            'properties' => [
                new class(name: 'tags') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ];
                    }
                },
            ],
            'expected' => [
                'tags' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
        ];
    }

    #[DataProvider('toJsonSchemaDataProvider')]
    public function test_converts_to_json_schema(array $properties, array $expected): void
    {
        // Arrange

        $schema = new Schema(properties: $properties);

        // Act

        $result = $schema->toJsonSchema();

        // Assert

        $this->assertEquals($expected, $result);
    }

    public static function toJsonSchemaDataProvider(): Generator
    {
        yield 'empty schema' => [
            'properties' => [],
            'expected' => [
                '$schema' => 'https://json-schema.org/draft/2020-12/schema',
                'type' => 'object',
                'properties' => [],
                'required' => [],
                'additionalProperties' => false,
            ],
        ];

        yield 'single non-required property' => [
            'properties' => [
                new class(name: 'name') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string'];
                    }
                },
            ],
            'expected' => [
                '$schema' => 'https://json-schema.org/draft/2020-12/schema',
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                ],
                'required' => [],
                'additionalProperties' => false,
            ],
        ];

        yield 'single required property' => [
            'properties' => [
                new class(name: 'email', required: true) extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string', 'format' => 'email'];
                    }
                },
            ],
            'expected' => [
                '$schema' => 'https://json-schema.org/draft/2020-12/schema',
                'type' => 'object',
                'properties' => [
                    'email' => ['type' => 'string', 'format' => 'email'],
                ],
                'required' => ['email'],
                'additionalProperties' => false,
            ],
        ];

        yield 'mixed required and optional properties' => [
            'properties' => [
                new class(name: 'name', required: true) extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string'];
                    }
                },
                new class(name: 'age') extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'integer'];
                    }
                },
                new class(name: 'email', required: true) extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string', 'format' => 'email'];
                    }
                },
            ],
            'expected' => [
                '$schema' => 'https://json-schema.org/draft/2020-12/schema',
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'integer'],
                    'email' => ['type' => 'string', 'format' => 'email'],
                ],
                'required' => ['name', 'email'],
                'additionalProperties' => false,
            ],
        ];

        yield 'all required properties' => [
            'properties' => [
                new class(name: 'id', required: true) extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string', 'format' => 'uuid'];
                    }
                },
                new class(name: 'name', required: true) extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string'];
                    }
                },
                new class(name: 'email', required: true) extends SchemaProperty
                {
                    public function toArray(): array
                    {
                        return ['type' => 'string', 'format' => 'email'];
                    }
                },
            ],
            'expected' => [
                '$schema' => 'https://json-schema.org/draft/2020-12/schema',
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'string', 'format' => 'uuid'],
                    'name' => ['type' => 'string'],
                    'email' => ['type' => 'string', 'format' => 'email'],
                ],
                'required' => ['id', 'name', 'email'],
                'additionalProperties' => false,
            ],
        ];
    }
}

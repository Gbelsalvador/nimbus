<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Schemas\ValueObjects;

use Closure;
use Generator;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\SchemaProperty;

#[CoversClass(SchemaProperty::class)]
class SchemaPropertyUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    #[DataProvider('toArrayDataProvider')]
    public function test_to_array(SchemaProperty|Closure $property, array $expected): void
    {
        // Arrange

        // Some properties have mock, so we need to evaluate the constructor here and not in the data provider.
        // This will prevent flakiness as the data providers are called first (in random order).
        // If all mocks are called, then the test will fail as it won't satisfy all expectations.
        $property = value($property);

        // Act

        $result = $property->toArray();

        // Assert

        $this->assertEquals($expected, $result);
    }

    public static function toArrayDataProvider(): Generator
    {
        yield 'a minimal structure' => [
            'property' => new SchemaProperty(
                name: 'simpleProperty',
                type: 'string'
            ),
            'expected' => [
                'type' => 'string',
                'x-required' => false,
                'x-name' => 'simpleProperty',
            ],
        ];

        yield 'a formatted string' => [
            'property' => new SchemaProperty(
                name: 'emailProperty',
                type: 'string',
                format: 'email'
            ),
            'expected' => [
                'type' => 'string',
                'format' => 'email',
                'x-required' => false,
                'x-name' => 'emailProperty',
            ],
        ];

        yield 'an enum' => [
            'property' => new SchemaProperty(
                name: 'statusProperty',
                type: 'string',
                enum: ['active', 'inactive', 'pending']
            ),
            'expected' => [
                'type' => 'string',
                'enum' => ['active', 'inactive', 'pending'],
                'x-required' => false,
                'x-name' => 'statusProperty',
            ],
        ];

        yield 'an array of primitives' => [
            'property' => fn () => new SchemaProperty(
                name: 'arrayProperty',
                type: 'array',
                itemsSchema: new SchemaProperty(
                    name: 'item',
                    type: 'integer',
                ),
            ),
            'expected' => [
                'type' => 'array',
                'items' => [
                    'type' => 'integer',
                    'x-required' => false,
                    'x-name' => 'item',
                ],
                'x-required' => false,
                'x-name' => 'arrayProperty',
            ],
        ];

        yield 'an array of objects' => [
            'property' => fn () => new SchemaProperty(
                name: 'arrayProperty',
                type: 'array',
                itemsSchema: new SchemaProperty(
                    name: 'item',
                    type: 'object',
                    propertiesSchema: self::getSchemaMock(
                        [
                            new SchemaProperty(
                                name: 'productId',
                                required: true,
                            ),
                        ],
                        required: ['productId'],
                    ),
                )
            ),
            'expected' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'productId' => [
                            'type' => 'string',
                            'x-required' => true,
                            'x-name' => 'productId',
                        ],
                    ],
                    'required' => ['productId'],
                    'x-required' => false,
                    'x-name' => 'item',
                ],
                'x-required' => false,
                'x-name' => 'arrayProperty',
            ],
        ];

        yield 'an object' => [
            'property' => fn () => new SchemaProperty(
                name: 'addressProperty',
                type: 'object',
                propertiesSchema: self::getSchemaMock(
                    properties: [
                        new SchemaProperty(
                            name: 'street',
                            type: 'string',
                            required: true,
                        ),
                        new SchemaProperty(
                            name: 'city',
                            type: 'string',
                        ),
                    ],
                    required: [
                        'street',
                    ]
                ),
            ),
            'expected' => [
                'type' => 'object',
                'properties' => [
                    'street' => [
                        'type' => 'string',
                        'x-required' => true,
                        'x-name' => 'street',
                    ],
                    'city' => [
                        'type' => 'string',
                        'x-required' => false,
                        'x-name' => 'city',
                    ],
                ],
                'required' => ['street'],
                'x-required' => false,
                'x-name' => 'addressProperty',
            ],
        ];

        yield 'an object without required fields' => [
            'property' => fn () => new SchemaProperty(
                name: 'addressProperty',
                type: 'object',
                required: true,
                propertiesSchema: self::getSchemaMock(
                    properties: [
                        new SchemaProperty(
                            name: 'street',
                            type: 'string',
                        ),
                        new SchemaProperty(
                            name: 'city',
                            type: 'string',
                        ),
                    ],
                    required: []
                ),
            ),
            'expected' => [
                'type' => 'object',
                'properties' => [
                    'street' => [
                        'type' => 'string',
                        'x-required' => false,
                        'x-name' => 'street',
                    ],
                    'city' => [
                        'type' => 'string',
                        'x-required' => false,
                        'x-name' => 'city',
                    ],
                ],
                'required' => [],
                'x-required' => true,
                'x-name' => 'addressProperty',
            ],
        ];

        yield 'a full structure' => [
            'property' => new SchemaProperty(
                name: 'fullProperty',
                type: 'string',
                required: true,
                format: 'date-time',
                enum: ['val1', 'val2'],
            ),
            'expected' => [
                'type' => 'string',
                'format' => 'date-time',
                'enum' => ['val1', 'val2'],
                'x-required' => true,
                'x-name' => 'fullProperty',
            ],
        ];

        yield 'an integer with format' => [
            'property' => new SchemaProperty(
                name: 'ageProperty',
                type: 'integer',
                format: 'int32',
            ),
            'expected' => [
                'type' => 'integer',
                'format' => 'int32',
                'x-required' => false,
                'x-name' => 'ageProperty',
            ],
        ];

        yield 'a boolean' => [
            'property' => new SchemaProperty(
                name: 'isActiveProperty',
                type: 'boolean',
            ),
            'expected' => [
                'type' => 'boolean',
                'x-required' => false,
                'x-name' => 'isActiveProperty',
            ],
        ];
    }

    /*
     * Mocks.
     */

    private static function getSchemaMock(array $properties, array $required): Schema
    {
        /** @var Schema&Mockery\MockInterface $schemaMock */
        $schemaMock = Mockery::mock(Schema::class)->makePartial();

        $schemaMock->__construct($properties);

        $schemaMock->shouldReceive('getRequiredProperties')->andReturn($required);

        return $schemaMock;
    }
}

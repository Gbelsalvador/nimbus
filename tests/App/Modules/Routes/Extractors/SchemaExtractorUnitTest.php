<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors;

use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Modules\Routes\Extractor\SchemaExtractor;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Strategies\ExtractorStrategyContract;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Strategies\FormRequestExtractorStrategy;
use Sunchayn\Nimbus\Modules\Routes\Extractor\Strategies\InlineRequestValidatorExtractorStrategy;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\ExtractableRoute;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\Schema;

#[CoversClass(SchemaExtractor::class)]
class SchemaExtractorUnitTest extends TestCase
{
    public function test_it_initialize_extractor_correctly(): void
    {
        // Arrange

        $formRequestExtractorStrategyMock = Mockery::mock(FormRequestExtractorStrategy::class);
        $inlineRequestValidatorExtractorStrategyMock = Mockery::mock(InlineRequestValidatorExtractorStrategy::class);
        $containerMock = Mockery::mock(Container::class);

        $this->swapMocksInDIContainer(
            $containerMock,
            implementations: [
                FormRequestExtractorStrategy::class => $formRequestExtractorStrategyMock,
                InlineRequestValidatorExtractorStrategy::class => $inlineRequestValidatorExtractorStrategyMock,
            ],
        );

        // Act

        $schemaExtractor = new SchemaExtractor($containerMock);

        // Assert

        $strategies = invade($schemaExtractor)->strategies;

        $this->assertCount(2, $strategies);

        $this->assertSame($formRequestExtractorStrategyMock, $strategies[0]);

        $this->assertSame($inlineRequestValidatorExtractorStrategyMock, $strategies[1]);
    }

    public function test_it_extracts_using_the_matching_strategy(): void
    {
        // Arrange

        $containerMock = Mockery::mock(
            Container::class,
            function (MockInterface $mock) {
                // Ignore existent strategies.
                $mock->shouldReceive('make')->withAnyArgs()->andReturnNull();
            },
        );

        [$matchingStrategyMock, $nonMatchingStrategyMock] = $this->makeMatchingStrategies();

        // Make schema extractor with the stub strategies.
        $schemaExtractor = new SchemaExtractor($containerMock);

        invade($schemaExtractor)->strategies = Arr::shuffle([
            $matchingStrategyMock,
            $nonMatchingStrategyMock,
        ]);

        $route = new ExtractableRoute(
            parameters: [],
            codeParser: fn () => 'noop',
        );

        // Act

        $schemaExtractor->extract($route);

        // Assert

        $matchingStrategyMock
            ->shouldHaveReceived('extract')
            ->withArgs(function (ExtractableRoute $routeArg) use ($route) {
                $this->assertSame($route, $routeArg);

                return true;
            })
            ->once();

        $nonMatchingStrategyMock->shouldNotHaveReceived('extract');
    }

    /*
     * Mocks.
     */

    private function swapMocksInDIContainer(mixed $containerMock, array $implementations): void
    {
        foreach ($implementations as $abstract => $implementation) {
            $containerMock
                ->shouldReceive('make')
                ->with($abstract)
                ->once()
                ->andReturn($implementation);
        }
    }

    /*
     * Generators.
     */

    private function makeMatchingStrategies(): array
    {
        $nonMatchingStrategy = new class implements ExtractorStrategyContract
        {
            public function matches(ExtractableRoute $route): bool
            {
                return false;
            }

            public function extract(ExtractableRoute $route): Schema
            {
                return Schema::empty();
            }
        };

        $matchingStrategy = new class implements ExtractorStrategyContract
        {
            public function matches(ExtractableRoute $route): bool
            {
                return true;
            }

            public function extract(ExtractableRoute $route): Schema
            {
                return Schema::empty();
            }
        };

        return [
            Mockery::mock($matchingStrategy)->makePartial(),
            Mockery::mock($nonMatchingStrategy)->makePartial(),
        ];
    }
}

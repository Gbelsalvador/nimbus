<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\ValueObjects;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Sunchayn\Nimbus\Modules\Routes\ValueObjects\ExtractableRoute;
use Sunchayn\Nimbus\Tests\TestCase;

#[CoversClass(ExtractableRoute::class)]
class ExtractableRouteUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_creates_empty_objects(): void
    {
        // Act

        $actual = ExtractableRoute::empty();

        // Assert

        $this->assertEmpty($actual->parameters);

        $this->assertEmpty(($actual->codeParser)());

        $this->assertNull($actual->methodName);

        $this->assertNull($actual->controllerClass);

        $this->assertNull($actual->controllerMethod);
    }
}

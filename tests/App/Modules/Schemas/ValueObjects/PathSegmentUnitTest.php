<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Schemas\ValueObjects;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Modules\Schemas\ValueObjects\PathSegment;

#[CoversClass(PathSegment::class)]
class PathSegmentUnitTest extends TestCase
{
    public function test_it_identifies_array_segments(): void
    {
        $segment = new PathSegment('*');

        $this->assertTrue($segment->isArray());
    }

    public function test_it_returns_false_for_non_array_segments(): void
    {
        $segment = new PathSegment('user');

        $this->assertFalse($segment->isArray());
    }
}

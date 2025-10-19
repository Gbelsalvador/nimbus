<?php

namespace Sunchayn\Nimbus\Tests;

use Illuminate\Support\Facades\Http;
use Mockery;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Sunchayn\Nimbus\NimbusServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'nimbus.prefix' => 'nimbus',
            'nimbus.routes.prefix' => 'api',
            'nimbus.routes.versioned' => false,
            'nimbus.headers' => [
                'x-request-id' => 'uuid',
                'x-session-id' => 'uuid',
            ],
            'force',
        ]);

        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        if ($container = Mockery::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        Mockery::close();

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            NimbusServiceProvider::class,
        ];
    }
}

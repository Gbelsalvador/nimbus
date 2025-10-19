<?php

namespace Sunchayn\Nimbus\Tests\Integration;

use Generator;
use Illuminate\Support\Facades\Vite;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Sunchayn\Nimbus\Http\Web\Controllers\NimbusIndexController;
use Sunchayn\Nimbus\Modules\Routes\Actions\BuildCurrentUserAction;
use Sunchayn\Nimbus\Modules\Routes\Actions\BuildGlobalHeadersAction;
use Sunchayn\Nimbus\Modules\Routes\Actions\DisableThirdPartyUiAction;
use Sunchayn\Nimbus\Modules\Routes\Actions\ExtractRoutesAction;
use Sunchayn\Nimbus\Modules\Routes\Collections\ExtractedRoutesCollection;
use Sunchayn\Nimbus\Modules\Routes\Exceptions\RouteExtractionException;
use Sunchayn\Nimbus\Modules\Routes\Services\IgnoredRoutesService;
use Sunchayn\Nimbus\Tests\TestCase;

#[CoversClass(NimbusIndexController::class)]
#[CoversClass(NimbusIndexController::class)]
class NimbusIndexTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router
            ->post('/api/test', fn () => response()->json(['message' => 'success']))
            ->name('api.test');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Vite to prevent asset loading issues
        Vite::shouldReceive('useBuildDirectory')->with('/vendor/nimbus')->once();
        Vite::shouldReceive('useHotFile')->with(base_path('/vendor/sunchayn/nimbus/resources/dist/hot'))->once();
        Vite::shouldReceive('__invoke')->andReturn('<script src="/nimbus/app.js"></script>');
    }

    #[DataProvider('indexRouteProvider')]
    public function test_it_loads_view_correctly(string $uri): void
    {
        // Arrange

        $disableThirdPartyUiActionSpy = $this->spy(DisableThirdPartyUiAction::class);
        $ignoreRoutesServiceSpy = $this->spy(IgnoredRoutesService::class);
        $buildGlobalHeadersActionMock = $this->mock(BuildGlobalHeadersAction::class);
        $buildCurrentUserActionMock = $this->mock(BuildCurrentUserAction::class);
        $extractRoutesActionMock = $this->mock(ExtractRoutesAction::class);

        // Anticipate

        $buildGlobalHeadersActionMock->shouldReceive('execute')->andReturn(['::global-headers::']);

        $buildCurrentUserActionMock->shouldReceive('execute')->andReturn(['::current-user::']);

        $extractedRoutesCollectionStub = new class extends ExtractedRoutesCollection
        {
            public function toFrontendArray(): array
            {
                return ['::extracted-routes::'];
            }
        };

        $extractRoutesActionMock->shouldReceive('execute')->andReturn($extractedRoutesCollectionStub);

        // Act

        $response = $this->get(route('nimbus.index').$uri);

        // Assert

        $response->assertStatus(200);

        $response->assertViewIs('nimbus::app');

        $response->assertViewHas('routes', ['::extracted-routes::']);

        $response->assertViewHas('headers', ['::global-headers::']);

        $response->assertViewHas('currentUser', ['::current-user::']);

        $disableThirdPartyUiActionSpy->shouldHaveReceived('execute')->once();

        $ignoreRoutesServiceSpy->shouldNotHaveReceived('execute');
    }

    public static function indexRouteProvider(): Generator
    {
        yield 'index route handles route extraction exception' => [
            'uri' => '/',
        ];

        yield 'index route with catch all parameter' => [
            'uri' => '/some/deep/path',
        ];
    }

    public function test_it_ignores_routes(): void
    {
        // Arrange

        $ignoreData = [
            'uri' => '/api/test',
            'methods' => ['POST'],
            'reason' => 'Test ignore',
        ];

        $url = route('nimbus.index', ['ignore' => urlencode(json_encode($ignoreData))]);

        // Act

        $response = $this->get($url);

        // Assert

        $response
            ->assertStatus(302)
            ->assertRedirect('/nimbus');
    }

    public function test_it_catches_extraction_errors(): void
    {
        // Arrange

        $disableThirdPartyUiActionSpy = $this->spy(DisableThirdPartyUiAction::class);
        $ignoreRoutesServiceSpy = $this->spy(IgnoredRoutesService::class);
        $buildGlobalHeadersActionSpy = $this->spy(BuildGlobalHeadersAction::class);
        $buildCurrentUserActionSpy = $this->spy(BuildCurrentUserAction::class);

        $extractionRoutesActionMock = $this->mock(ExtractRoutesAction::class);

        $exception = new class(message: fake()->words(asText: true), routeUri: fake()->url(), routeMethods: fake()->words(2), controllerClass: fake()->word(), controllerMethod: fake()->word(), suggestedSolution: fake()->words(asText: true)) extends RouteExtractionException {};

        // Anticipate

        $extractionRoutesActionMock->shouldReceive('execute')->andThrow($exception);

        // Act

        $response = $this->get(route('nimbus.index'));

        // Assert

        $response->assertStatus(200);

        $response->assertViewIs('nimbus::app');

        $response->assertViewHas(
            'routeExtractorException',
            [
                'exception' => [
                    'message' => $exception->getMessage(),
                    'previous' => $exception->getPrevious() ? [
                        'message' => $exception->getPrevious()->getMessage(),
                        'file' => $exception->getPrevious()->getFile(),
                        'line' => $exception->getPrevious()->getLine(),
                    ] : null,
                ],
                'routeContext' => $exception->getRouteContext(),
                'suggestedSolution' => $exception->getSuggestedSolution(),
                'ignoreData' => $exception->getIgnoreData(),
            ],
        );

        $disableThirdPartyUiActionSpy->shouldHaveReceived('execute')->once();
        $ignoreRoutesServiceSpy->shouldNotHaveReceived('execute');
        $buildGlobalHeadersActionSpy->shouldNotHaveReceived('execute');
        $buildCurrentUserActionSpy->shouldNotHaveReceived('execute');
    }

    public function test_it_integrates(): void
    {
        // Act

        $response = $this->get(route('nimbus.index'));

        // Assert

        $response->assertStatus(200);

        $response->assertViewIs('nimbus::app');

        $response->assertViewHas(['routes', 'headers', 'currentUser']);
    }
}

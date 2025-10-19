<?php

namespace Sunchayn\Nimbus\Http\Web\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Sunchayn\Nimbus\Modules\Routes\Actions;
use Sunchayn\Nimbus\Modules\Routes\Exceptions\RouteExtractionException;

class NimbusIndexController
{
    private const VIEW_NAME = 'nimbus::app';

    public function __invoke(
        Actions\ExtractRoutesAction $extractRoutesAction,
        Actions\IgnoreRouteErrorAction $ignoreRouteErrorAction,
        Actions\BuildGlobalHeadersAction $buildGlobalHeadersAction,
        Actions\BuildCurrentUserAction $buildCurrentUserAction,
        Actions\DisableThirdPartyUiAction $disableThirdPartyUiAction,
    ): Renderable|RedirectResponse {
        $disableThirdPartyUiAction->execute();

        Vite::useBuildDirectory('/vendor/nimbus');
        Vite::useHotFile(base_path('/vendor/sunchayn/nimbus/resources/dist/hot'));

        if (request()->has('ignore')) {
            $ignoreRouteErrorAction->execute(
                ignoreData: request()->get('ignore'),
            );

            return redirect()->to(request()->url());
        }

        try {
            $routes = $extractRoutesAction
                ->execute(
                    routes: RouteFacade::getRoutes()->getRoutes(),
                );
        } catch (RouteExtractionException $routeExtractionException) {
            return view(self::VIEW_NAME, [ // @phpstan-ignore-line it cannot find the view.
                'routeExtractorException' => $this->renderExtractorException($routeExtractionException),
            ]);
        }

        return view(self::VIEW_NAME, [ // @phpstan-ignore-line it cannot find the view.
            'routes' => $routes->toFrontendArray(),
            'headers' => $buildGlobalHeadersAction->execute(),
            'currentUser' => $buildCurrentUserAction->execute(),
        ]);
    }

    /**
     * @return array<string, array<string, array<int|string>|string|null>|string|null>
     */
    private function renderExtractorException(RouteExtractionException $routeExtractionException): array
    {
        return [
            'exception' => [
                'message' => $routeExtractionException->getMessage(),
                'previous' => $routeExtractionException->getPrevious() instanceof \Throwable ? [
                    'message' => $routeExtractionException->getPrevious()->getMessage(),
                    'file' => $routeExtractionException->getPrevious()->getFile(),
                    'line' => $routeExtractionException->getPrevious()->getLine(),
                    'trace' => Str::replace("\n", '<br/>', $routeExtractionException->getPrevious()->getTraceAsString()),
                ] : null,
            ],
            'routeContext' => $routeExtractionException->getRouteContext(),
            'suggestedSolution' => $routeExtractionException->getSuggestedSolution(),
            'ignoreData' => $routeExtractionException->getIgnoreData(),
        ];
    }
}

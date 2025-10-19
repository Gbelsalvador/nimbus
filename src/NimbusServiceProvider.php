<?php

namespace Sunchayn\Nimbus;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sunchayn\Nimbus\Modules\Routes\Services\IgnoredRoutesService;

class NimbusServiceProvider extends PackageServiceProvider
{
    private bool $enabled;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->enabled = $this->app->environment(config('nimbus.allowed_envs', ['testing', 'local', 'staging']));
    }

    /**
     * @see https://github.com/spatie/laravel-package-tools
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nimbus')
            ->hasConfigFile()
            ->hasViews('nimbus')
            ->hasAssets()
            ->hasRoutes(['api', 'web']);
    }

    public function register(): void
    {
        if (! $this->enabled) {
            $this->registerPackageConfigs();

            return;
        }

        parent::register();

        $this->app->singleton(
            IgnoredRoutesService::class,
            fn (): \Sunchayn\Nimbus\Modules\Routes\Services\IgnoredRoutesService => new IgnoredRoutesService,
        );
    }

    public function boot(): void
    {
        if (! $this->enabled) {
            return;
        }

        parent::boot();
    }
}

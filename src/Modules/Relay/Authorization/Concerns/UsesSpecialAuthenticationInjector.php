<?php

namespace Sunchayn\Nimbus\Modules\Relay\Authorization\Concerns;

use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Sunchayn\Nimbus\Modules\Config\Exceptions\MisconfiguredValueException;
use Sunchayn\Nimbus\Modules\Relay\Authorization\Contracts\SpecialAuthenticationInjectorContract;

trait UsesSpecialAuthenticationInjector
{
    /**
     * @throws BindingResolutionException
     * @throws MisconfiguredValueException
     */
    public function getInjector(Container $container, Repository $configRepository): SpecialAuthenticationInjectorContract
    {
        /** @var ?class-string $injectorClass */
        $injectorClass = $configRepository->get('nimbus.auth.special.injector');

        if ($injectorClass === null) {
            throw MisconfiguredValueException::becauseSpecialAuthenticationInjectorIsInvalid();
        }

        $injector = $container->make($injectorClass);

        if (! $injector instanceof SpecialAuthenticationInjectorContract) {
            throw MisconfiguredValueException::becauseSpecialAuthenticationInjectorIsInvalid();
        }

        return $injector;
    }
}

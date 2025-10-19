<?php

namespace Sunchayn\Nimbus\Modules\Relay\Authorization\Handlers;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\PendingRequest;
use Sunchayn\Nimbus\Modules\Config\Exceptions\MisconfiguredValueException;
use Sunchayn\Nimbus\Modules\Relay\Authorization\Concerns\UsesSpecialAuthenticationInjector;
use Sunchayn\Nimbus\Modules\Relay\Authorization\Exceptions\InvalidAuthorizationValueException;

class ImpersonateUserAuthorizationHandler implements AuthorizationHandler
{
    use UsesSpecialAuthenticationInjector;

    private UserProvider $userProvider;

    public function __construct(
        public readonly int $userId,
        private readonly Container $container,
        private readonly ConfigRepository $configRepository,
    ) {
        if ($userId <= 0) {
            throw InvalidAuthorizationValueException::becauseUserIsNotFound();
        }

        $this->userProvider = $this
            ->container->get('auth')
            ->guard(name: config('nimbus.auth.guard'))
            ->getProvider();
    }

    /**
     * @throws BindingResolutionException
     * @throws MisconfiguredValueException
     */
    public function authorize(PendingRequest $pendingRequest): PendingRequest
    {
        $user = $this->userProvider->retrieveById($this->userId);

        if ($user === null) {
            throw InvalidAuthorizationValueException::becauseUserIsNotFound();
        }

        return $this
            ->getInjector($this->container, $this->configRepository)
            ->attach($pendingRequest, $user);
    }
}

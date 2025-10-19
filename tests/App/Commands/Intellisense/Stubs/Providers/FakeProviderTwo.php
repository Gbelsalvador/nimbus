<?php

namespace Sunchayn\Nimbus\Tests\App\Commands\Intellisense\Stubs\Providers;

class FakeProviderTwo extends FakeProviderOne
{
    public function getStub(): string
    {
        return 'fake-provider-two.ts.stub';
    }
}

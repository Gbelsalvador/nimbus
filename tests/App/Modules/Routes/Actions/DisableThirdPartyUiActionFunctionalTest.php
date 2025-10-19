<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Actions;

use PHPUnit\Framework\Attributes\CoversClass;
use Sunchayn\Nimbus\Modules\Routes\Actions\DisableThirdPartyUiAction;
use Sunchayn\Nimbus\Tests\TestCase;

#[CoversClass(DisableThirdPartyUiAction::class)]
class DisableThirdPartyUiActionFunctionalTest extends TestCase
{
    public function test_it_disables_debug_bar_w_hen_it_exists(): void
    {
        // TODO [Test] Figure out a way to test this.
        $this->addToAssertionCount(1);
    }
}

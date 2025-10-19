<?php

namespace Sunchayn\Nimbus\Modules\Routes\Actions;

/**
 * Disables third-party UI addons that interfere with Nimbus interface.
 */
class DisableThirdPartyUiAction
{
    public function execute(): void
    {
        // The Debugbar will interfere with the UI,
        // The page is a third party so most likely no need to have the debug toolbar here.
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }
    }
}

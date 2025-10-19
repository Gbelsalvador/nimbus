<?php

use Illuminate\Support\Facades\Route;
use Sunchayn\Nimbus\Http\Api\Relay\NimbusRelayController;

Route::group(
    [
        'domain' => config('nimbus.domain'),
        'prefix' => config('nimbus.prefix').'/api',
    ],
    function () {
        Route::post('/relay', NimbusRelayController::class)
            ->name('nimbus.api.relay');
    },
);

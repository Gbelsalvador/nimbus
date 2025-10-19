<?php

use Illuminate\Support\Facades\Route;
use Sunchayn\Nimbus\Http\Web\Controllers\NimbusIndexController;

Route::group(
    [
        'domain' => config('nimbus.domain'),
        'prefix' => config('nimbus.prefix'),
        'middleware' => 'web',
    ],
    function () {
        Route::get('/{view?}', NimbusIndexController::class)
            ->where('view', '(.*)')
            ->name('nimbus.index');
    },
);

<?php

namespace Sunchayn\Nimbus\Http\Api\Relay;

use Illuminate\Http\Resources\Json\JsonResource;
use Sunchayn\Nimbus\Modules\Relay\Actions\RequestRelayAction;
use Sunchayn\Nimbus\Modules\Relay\DataTransferObjects\RequestRelayData;

/**
 * Relays the Execution of HTTP requests with authorization and returns structured response data.
 *
 *  A relay endpoint is needed to access HTTP Only cookies,
 *  and deal with Laravel specific details for response/request life-cycle.
 */
class NimbusRelayController
{
    public function __invoke(
        NimbusRelayRequest $nimbusRelayRequest,
        RequestRelayAction $requestRelayAction,
    ): JsonResource {
        $relayedRequestResponseData = $requestRelayAction
            ->execute(
                RequestRelayData::fromRelayApiRequest($nimbusRelayRequest),
            );

        return RelayResponseResource::make($relayedRequestResponseData);
    }
}

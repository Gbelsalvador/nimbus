<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Relay\DataTransferObjects;

use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sunchayn\Nimbus\Http\Api\Relay\NimbusRelayRequest;
use Sunchayn\Nimbus\Modules\Relay\Authorization\AuthorizationTypeEnum;
use Sunchayn\Nimbus\Modules\Relay\DataTransferObjects\RequestRelayData;
use Symfony\Component\HttpFoundation\InputBag;

#[CoversClass(RequestRelayData::class)]
class RequestRelayDataUnitTest extends TestCase
{
    public function test_it_creates_instance_from_api_request(): void
    {
        // Arrange

        $mockRequest = Mockery::mock(NimbusRelayRequest::class);

        $mockRequest->shouldReceive('userAgent')->andReturn('::dummy_user_agent::');

        $mockRequest->shouldReceive('host')->andReturn('::dummy_host::');

        $mockCookies = new InputBag;

        $mockRequest->cookies = $mockCookies;

        $stubAuthorizationType = AuthorizationTypeEnum::Bearer;

        // Anticipate

        $mockRequest
            ->shouldReceive('validated')
            ->andReturn(
                [
                    'method' => $method = 'POST',
                    'endpoint' => $endpoint = '/api/test',
                    'authorization' => [
                        'type' => $stubAuthorizationType->value,
                        'value' => $authorizationValue = 'foobar',
                    ],
                    'headers' => [
                        ['key' => 'Content-Type', 'value' => 'application/json'],
                        ['key' => 'X-Custom-Header', 'value' => '::value::'],
                    ],
                    'body' => $body = ['test' => 'data'],
                ],
            );

        $mockRequest->shouldReceive('getBody')->andReturn($body);

        // Act

        $result = RequestRelayData::fromRelayApiRequest($mockRequest);

        // Assert

        $this->assertEquals(strtolower($method), $result->method);

        $this->assertEquals($endpoint, $result->endpoint);

        $this->assertEquals($stubAuthorizationType, $result->authorization->type);

        $this->assertEquals($authorizationValue, $result->authorization->value);

        $this->assertEquals(
            [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'x-custom-header' => '::value::',
                'user-agent' => '::dummy_user_agent::',
            ],
            $result->headers);

        $this->assertEquals($body, $result->body);

        $this->assertSame($mockCookies, $result->cookies);
    }
}

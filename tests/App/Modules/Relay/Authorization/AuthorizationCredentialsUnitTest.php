<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Relay\Authorization;

use PHPUnit\Framework\Attributes\CoversClass;
use Sunchayn\Nimbus\Modules\Relay\Authorization\AuthorizationCredentials;
use Sunchayn\Nimbus\Modules\Relay\Authorization\AuthorizationTypeEnum;
use Sunchayn\Nimbus\Tests\TestCase;

#[CoversClass(AuthorizationCredentials::class)]
class AuthorizationCredentialsUnitTest extends TestCase
{
    public function test_it_constructs_none_state(): void
    {
        $credentials = AuthorizationCredentials::none();

        // Assert

        $this->assertEquals(AuthorizationTypeEnum::None, $credentials->type);
        $this->assertNull($credentials->value);
    }
}

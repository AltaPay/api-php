<?php

namespace Altapay\ApiTest\Functional;

use Altapay\ApiTest\TestAuthentication;
use Altapay\Authentication;
use Altapay\Exceptions\ClientException;
use GuzzleHttp\Exception\ConnectException;

class TestAuthenticationTest extends AbstractFunctionalTest
{
    public function test_auth(): void
    {
        $response = (new TestAuthentication($this->getAuth()))->call();
        $this->assertSame('ok', $response);
    }

    public function test_auth_fails(): void
    {
        $this->expectException(ClientException::class);
        $response = (new TestAuthentication(new Authentication('username', 'password')))->call();
    }

    public function test_auth_fails_connection(): void
    {
        $this->expectException(ConnectException::class);
        $response = (new TestAuthentication(new Authentication(
            'username',
            'password',
            'http://doesnotexists.mecom'
        )))->call();
    }
}

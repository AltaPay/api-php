<?php

namespace Altapay\ApiTest\Functional;

use Altapay\ApiTest\TestConnection;
use GuzzleHttp\Exception\ConnectException;

class TestConnectionTest extends AbstractFunctionalTest
{
    public function test_connection(): void
    {
        $response = (new TestConnection())
            ->call();

        $this->assertSame('ok', $response);
    }

    public function test_connection_fails(): void
    {
        $this->expectException(ConnectException::class);
        $response = (new TestConnection('http//idonotexists.mecom'))
            ->call();
    }
}

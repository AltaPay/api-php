<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Api\Others\Terminals;
use Altapay\Response\TerminalsResponse;

class TerminalsTest extends AbstractFunctionalTest
{
    public function test_terminals(): void
    {
        $response = (new Terminals($this->getAuth()))->call();
        $this->assertInstanceOf(TerminalsResponse::class, $response);
        $this->assertTrue(is_numeric($_ENV['NUMBER_OF_TERMINALS']));
        $this->assertCount((int)$_ENV['NUMBER_OF_TERMINALS'], $response->Terminals);
    }
}

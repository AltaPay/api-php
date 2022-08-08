<?php

namespace Altapay\ApiTest\Request;

use Altapay\Request\OrderLine;

class OrderLineRequestTestSerializer extends OrderLine
{
    public function serialize()
    {
        if ($this->get($this, 'foobar') === false) {
            throw new \Exception('Got false');
        }

        return [];
    }
}

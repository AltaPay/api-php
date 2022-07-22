<?php

namespace Altapay\Api\Ecommerce;

use Altapay\Response\Embeds\Address;

class ResponseInfo extends Callback
{
    /**
     * @return Address|null
     */
    public function getRegisteredAddress()
    {
        $response          = $this->call();
        $registeredAddress = null;
        if (isset($response->Transactions[0]->CustomerInfo->RegisteredAddress)) {
            $registeredAddress = $response->Transactions[0]->CustomerInfo->RegisteredAddress;
        }

        return $registeredAddress;
    }
}

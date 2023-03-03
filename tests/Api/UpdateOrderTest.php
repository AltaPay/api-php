<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Payments\UpdateOrder;
use Altapay\Response\Embeds\Transaction;
use Altapay\Response\UpdateOrderResponse as UpdateOrderDocument;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class UpdateOrderTest extends AbstractApiTest
{
    /**
     * @return UpdateOrder
     */
    protected function getUpdateOrder()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/updateorder.xml');

        return (new UpdateOrder($this->getAuth()))
            ->setClient($client);
    }

    public function test_update_order(): void
    {
        $api = $this->getUpdateOrder();
        $api->setPaymentId('123');
        $api->setAmount(123);
        $this->assertInstanceOf(UpdateOrderDocument::class, $api->call());
    }

    /**
     * @depends test_update_order
     */
    public function test_update_order_data(): void
    {
        $api = $this->getUpdateOrder();
        $api->setPaymentId('123');
        $api->setAmount(123);
        $response = $api->call();
        $this->assertInstanceOf(UpdateOrderDocument::class, $response);
        $this->assertSame('Success', $response->Result);
    }
}

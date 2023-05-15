<?php

namespace Altapay\ApiTest\Api;

use Altapay\Request\Address;
use Altapay\Request\Customer;
use Altapay\Request\OrderLine;
use Altapay\ApiTest\AbstractTest;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

abstract class AbstractApiTest extends AbstractTest
{
    /**
     * @return Client
     */
    protected function getClient(MockHandler $mock)
    {
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }

    protected function getXmlClient(string $xmlPath): Client
    {
        return $this->getClient(new MockHandler([
            new Response(
                200,
                ['text-content' => 'application/xml'],
                file_get_contents($xmlPath) ?: null
            )
        ]));
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    protected function getExceptedUri($uri)
    {
        return '/merchant/API/' . $uri;
    }

    /**
     * @return Customer
     */
    protected function getCustomerInfo()
    {
        $billing             = new Address();
        $billing->firstname  = 'First name';
        $billing->lastname   = 'Last name';
        $billing->address    = 'my address';
        $billing->city       = 'Somewhere';
        $billing->postalCode = '2000';
        $billing->region     = '0';
        $billing->country    = 'DK';

        $shipping             = new Address();
        $shipping->firstname  = 'First name';
        $shipping->lastname   = 'Last name';
        $shipping->address    = 'my address';
        $shipping->city       = 'Somewhere';
        $shipping->postalCode = '2000';
        $shipping->region     = '0';
        $shipping->country    = 'DK';

        $customer = new Customer($billing);
        $customer->setShipping($shipping);
        $customer->setCreatedDate(new \DateTime('2016-11-25'));

        return $customer;
    }

    /**
     * @return OrderLine[]
     */
    protected function getOrderLines()
    {
        $orderlines            = [];
        $orderline             = new OrderLine('White sugar', 'productid', 1.5, 5.75);
        $orderline->taxPercent = 20;
        $orderline->unitCode   = 'kg';
        $orderlines[]          = $orderline;

        $orderline             = new OrderLine('Brown sugar', 'productid2', 2.5, 8.75);
        $orderline->unitCode   = 'kg';
        $orderline->taxPercent = 20;
        $orderlines[]          = $orderline;

        return $orderlines;
    }
}

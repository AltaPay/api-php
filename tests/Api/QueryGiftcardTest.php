<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\QueryGiftcard;
use Altapay\Request\Giftcard;
use Altapay\Response\GiftcardResponse as GiftcardResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class QueryGiftcardTest extends AbstractApiTest
{
    /**
     * @return QueryGiftcard
     */
    protected function getapi()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/querygiftcard.xml');

        return (new QueryGiftcard($this->getAuth()))
            ->setClient($client);
    }

    public function test_route(): void
    {
        $card = new Giftcard('account', 'provider', '1234-1234');
        $api  = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setGiftcard($card);
        $api->call();
        $request = $api->getRawRequest();


        $this->assertSame($this->getExceptedUri('queryGiftCard'), $api->getRawRequest()->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);

        $this->assertSame('my terminal', $parts['terminal']);
        /** @var array<string> $giftcard */
        $giftcard = $parts['giftcard'];
        $this->assertSame('account', $giftcard['account_identifier']);
        $this->assertSame('provider', $giftcard['provider']);
        $this->assertSame('1234-1234', $giftcard['token']);
    }

    public function test_response(): void
    {
        $card = new Giftcard('account', 'provider', '1234-1234');
        $api  = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setGiftcard($card);
        $response = $api->call();
        $this->assertInstanceOf(GiftcardResponse::class, $response);

        $this->assertSame('Success', $response->Result);
        $this->assertCount(2, $response->Accounts);
        $account = $response->Accounts[0];

        $this->assertSame('EUR', $account->Currency);
        $this->assertSame('50.00', $account->Balance);
    }
}

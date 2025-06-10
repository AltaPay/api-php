<?php

namespace Altapay\ApiTest\Api\Api;

use Altapay\Api\Payments\ReservationOfFixedAmount;
use Altapay\Request\Address;
use Altapay\Request\Card;
use Altapay\Request\Customer;
use Altapay\Response\Embeds\Terminal;
use Altapay\Response\ReservationOfFixedAmountResponse;
use Altapay\Types\FraudServices;
use Altapay\Types\PaymentSources;
use Altapay\Types\PaymentTypes;
use Altapay\Types\ShippingMethods;
use Altapay\Types\TypeInterface;
use Altapay\ApiTest\Api\AbstractApiTest;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Altapay\Response\PaymentRequestResponse;
use Altapay\Types\LanguageTypes;

class ReservationOfFixedAmountTest extends AbstractApiTest
{
    /**
     * @return ReservationOfFixedAmount
     */
    protected function getapi()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/reservation.xml');

        return (new ReservationOfFixedAmount($this->getAuth()))
            ->setClient($client);
    }

    public function test_missing_all_options(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(
            'The required options "amount", "currency", "shop_orderid", "terminal" are missing.'
        );
        $this->getapi()->call();
    }

    public function test_missing_terminal_options(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(
            'The required option "terminal" is missing.'
        );

        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->call();
    }

    public function test_url(): void
    {
        $api = $this->getapi();

        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setSurcharge(155.23);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('my terminal', $parts['terminal']);
        $this->assertSame('order id', $parts['shop_orderid']);
        $this->assertSame('200.5', $parts['amount']);
        $this->assertSame('957', $parts['currency']);
        $this->assertSame('155.23', $parts['surcharge']);
    }

    public function test_terminal(): void
    {
        $terminal        = new Terminal();
        $terminal->Title = 'terminal object';

        $api = $this->getapi();
        $api->setTerminal($terminal);
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        
        $api->setSurcharge(155.23);
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('terminal object', $parts['terminal']);
    }

    public function test_wrong_currency(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            'The option "currency" with value "danske kroner" is invalid.'
        );

        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency('danske kroner');
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setSurcharge(155.23);
        $api->call();
    }

    public function test_creditcard_and_token(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'You can not set both a credit card and a credit card token'
        );

        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setCard(new Card('1234', '10', '12', '100'));
        $api->setCreditCardToken('token');
        $api->call();
    }

    public function test_token_and_creditcard(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'You can not set both a credit card token and a credit card'
        );

        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setCreditCardToken('token');
        $api->setCard(new Card('1234', '10', '12', '100'));
        $api->call();
    }

    public function test_creditcard_query(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setShopOrderId('order id');

        $api->setCard(new Card('1234', '10', '12', '100'));
        $api->call();
        $request = $api->getRawRequest();
        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('1234', $parts['cardnum']);
        $this->assertSame('12', $parts['eyear']);
        $this->assertSame('10', $parts['emonth']);
        $this->assertSame('100', $parts['cvc']);
    }

    public function test_creditcardtoken_query(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setCreditCardToken('credit card token', '200');
        $api->call();
        $request = $api->getRawRequest();
        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('credit card token', $parts['credit_card_token']);
        $this->assertSame('200', $parts['cvc']);
    }

    public function test_customer_query(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $api->setCustomerInfo($this->getCustomerInfo());
        $api->call();

        $request = $api->getRawRequest();
        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
    
        /** @var array<string> $customerInfo */
        $customerInfo = $parts['customer_info'];
        $this->assertSame('my address', $customerInfo['billing_address']);
        $this->assertSame('Last name', $customerInfo['billing_lastname']);
        $this->assertSame('2000', $customerInfo['billing_postal']);
        $this->assertSame('Somewhere', $customerInfo['billing_city']);
        $this->assertSame('0', $customerInfo['billing_region']);
        $this->assertSame('DK', $customerInfo['billing_country']);
        $this->assertSame('First name', $customerInfo['billing_firstname']);

        $this->assertSame('First name', $customerInfo['shipping_firstname']);
        $this->assertSame('Last name', $customerInfo['shipping_lastname']);
        $this->assertSame('my address', $customerInfo['shipping_address']);
        $this->assertSame('Somewhere', $customerInfo['shipping_city']);
        $this->assertSame('0', $customerInfo['shipping_region']);
        $this->assertSame('2000', $customerInfo['shipping_postal']);
        $this->assertSame('DK', $customerInfo['shipping_country']);

        $this->assertSame('2016-11-25', $parts['customer_created_date']);
    }

    public function test_customer_fullquery(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $billing             = new Address();
        $billing->Firstname  = 'First name';
        $billing->Lastname   = 'Last name';
        $billing->Address    = 'my address';
        $billing->City       = 'Somewhere';
        $billing->PostalCode = '2000';
        $billing->Region     = '0';
        $billing->Country    = 'DK';

        $shipping             = new Address();
        $shipping->Firstname  = 'First name';
        $shipping->Lastname   = 'Last name';
        $shipping->Address    = 'my address';
        $shipping->City       = 'Somewhere';
        $shipping->PostalCode = '2000';
        $shipping->Region     = '0';
        $shipping->Country    = 'DK';

        $customer = new Customer($billing);
        $customer->setShipping($shipping);
        $customer->setBirthdate(new \DateTime('2001-11-28'));
        $customer->setEmail('my@mail.com');
        $customer->setUsername('username');
        $customer->setPhone('50607080');
        $customer->setBankName('bank name');
        $customer->setCardHolderName('Test card');
        $customer->setBankPhone('20304050');

        $api->setCustomerInfo($customer);
        $api->call();

        $request = $api->getRawRequest();
        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
    
        /** @var array<string> $customerInfo */
        $customerInfo = $parts['customer_info'];
        $this->assertSame('2001-11-28', $customerInfo['birthdate']);
        $this->assertSame('my@mail.com', $customerInfo['email']);
        $this->assertSame('username', $customerInfo['username']);
        $this->assertSame('50607080', $customerInfo['customer_phone']);
        $this->assertSame('bank name', $customerInfo['bank_name']);
        $this->assertSame('20304050', $customerInfo['bank_phone']);
    }


    public function test_transaction_info(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $transactionInfo = ['Trans 1', 'Trans 2'];
        $api->setTransactionInfo($transactionInfo);
        $api->call();

        $request = $api->getRawRequest();
        $this->assertSame($this->getExceptedUri('reservation'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        /** @var array<array<int,string>> $parts['transaction_info'] */
        $this->assertCount(2, $parts['transaction_info']);
        $this->assertSame('Trans 2', $parts['transaction_info'][1]);
    }

    public function test_result(): void
    {
        $api = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $response = $api->call();

        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertCount(1, (array)$response->Transactions);
    }

    public function test_real_api_call_response(): void
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/reservation.xml');

        $api = (new ReservationOfFixedAmount($this->getAuth()))
            ->setClient($client);

        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setAgreement(
            [
                'id' => '232323232',
                'agreement_type' => 'unscheduled',
                'agreement[unscheduled]' => 'incremental'
            ]
        );
        $response = $api->call();
        $this->assertInstanceOf(ReservationOfFixedAmountResponse::class, $response);
    }

    /**
     * @param class-string<TypeInterface>|TypeInterface $class
     * @param string               $key
     * @param string               $setter
     */
    private function allowedTypes($class, $key, $setter): void
    {
        foreach (LanguageTypes::getAllowed() as $type) {
            $api = $this->getapi();
            $api->setTerminal('my terminal');
            $api->setAmount(200.50);
            $api->setCurrency(957);
            $api->setShopOrderId('order id');
            $api->{$setter}($type);
            $api->call();
            $request = $api->getRawRequest();
            parse_str($request->getUri()->getQuery(), $parts);
            $this->assertSame($type, $parts[$key]);

            $this->assertTrue($class::isAllowed($type));
        }

        $this->disallowedTypes($class, $key, $setter);
    }

    /**
     * @param class-string<TypeInterface>|TypeInterface $class
     * @param string               $key
     * @param string               $method
     */
    private function disallowedTypes($class, $key, $method): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The option "%s" with value "not allowed type" is invalid. Accepted values are: "%s".',
                $key,
                implode('", "', $class::getAllowed())
            )
        );

        $type = 'not allowed type';
        $api  = $this->getapi();
        $api->setTerminal('my terminal');
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->{$method}($type);
        $api->call();
        $this->assertFalse($class::isAllowed($type));
    }
}

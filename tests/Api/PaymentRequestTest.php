<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Ecommerce\PaymentRequest;
use Altapay\Request\Config;
use Altapay\Response\PaymentRequestResponse;
use Altapay\Types\LanguageTypes;
use Altapay\Types\TypeInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class PaymentRequestTest extends AbstractApiTest
{
    const CONFIG_URL = 'https://myshop.com/callback';

    /**
     * @return PaymentRequest
     */
    protected function getapi()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/paymentrequest.xml');

        return (new PaymentRequest($this->getAuth()))
            ->setClient($client);
    }

    public function test_required_options(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage(
            'The required options "amount", "currency", "shop_orderid", "terminal" are missing.'
        );

        $api = $this->getapi();
        $api->call();
    }

    public function test_required_url(): void
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('createPaymentRequest'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('my terminal', $parts['terminal']);
        $this->assertSame('order id', $parts['shop_orderid']);
        $this->assertSame('200.5', $parts['amount']);
        $this->assertSame('957', $parts['currency']);
    }

    public function test_options_url(): void
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $api->setLanguage('da');
        $api->setType('payment');
        $cctoken = $this->randomString(41);
        $api->setCcToken($cctoken);
        $api->setSaleReconciliationIdentifier('identifier');
        $api->setSaleInvoiceNumber('invoice number');
        $api->setSalesTax(15.55);
        $api->setCookie('cookie');
        $api->setPaymentSource('mail_order');
        $api->setCustomerInfo($this->getCustomerInfo());
        $api->setConfig($this->getConfig());
        $api->setFraudService('maxmind');
        $api->setShippingMethod('StorePickup');
        $api->setOrganisationNumber('my organisation');
        $api->setAccountOffer(true);
        $api->setOrderLines($this->getOrderLines());

        $api->call();
        $request = $api->getRawRequest();

        $this->assertSame($this->getExceptedUri('createPaymentRequest'), $request->getUri()->getPath());
        parse_str($request->getBody()->getContents(), $parts);
        $this->assertSame('da', $parts['language']);
        $this->assertIsNumeric($parts['amount'], 'Amount is not numeric');
        $this->assertIsNumeric($parts['currency'], 'Currency is not numeric');
        $this->assertSame('my terminal', $parts['terminal']);
        $this->assertSame('order id', $parts['shop_orderid']);
        $this->assertSame(200.50, ((float)$parts['amount']));
        $this->assertSame(957, ((int)$parts['currency']));
        $this->assertSame('payment', $parts['type']);
        $this->assertSame($cctoken, $parts['ccToken']);
        $this->assertSame('identifier', $parts['sale_reconciliation_identifier']);
        $this->assertSame('invoice number', $parts['sale_invoice_number']);
        $this->assertSame('15.55', $parts['sales_tax']);
        $this->assertSame('cookie', $parts['cookie']);
        $this->assertSame('mail_order', $parts['payment_source']);
        $this->assertSame('maxmind', $parts['fraud_service']);
        $this->assertSame('StorePickup', $parts['shipping_method']);
        $this->assertSame('my organisation', $parts['organisation_number']);
        $this->assertSame('required', $parts['account_offer']);

        // Orderlines
        /** @var array<array<int,string>> $parts['orderLines'] */
        $this->assertCount(2, $parts['orderLines']);
        /** @var array<string> $line */
        $line = $parts['orderLines'][1];
        $this->assertSame('Brown sugar', $line['description']);
        $this->assertSame('productid2', $line['itemId']);
        $this->assertSame('2.5', $line['quantity']);
        $this->assertSame('8.75', $line['unitPrice']);
        $this->assertSame('20', $line['taxPercent']);
        $this->assertSame('kg', $line['unitCode']);

        // Config
        /** @var array<string> $config */
        $config = $parts['config'];
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'form'), $config['callback_form']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'ok'), $config['callback_ok']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'fail'), $config['callback_fail']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'redirect'), $config['callback_redirect']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'open'), $config['callback_open']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'notification'), $config['callback_notification']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'verify'), $config['callback_verify_order']);
        $this->assertSame(sprintf('%s/%s', self::CONFIG_URL, 'mobile_app_redirect'), $config['callback_mobile_app_redirect']);

        // Customer info
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

        /** @var string $date */
        $date = $parts['customer_created_date'];
        $this->assertSame('2016-11-25', $date);
    }

    public function test_response(): void
    {
        $api = $this->getapi();
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $response = $api->call();

        $this->assertInstanceOf(PaymentRequestResponse::class, $response);
        $this->assertSame('Success', $response->Result);
        $this->assertSame('2349494a-6adf-49f7-8096-2125a969e104', $response->PaymentRequestId);
        $this->assertSame(
            'https://gateway.altapaysecure.com/merchant.php/API/requestForm?pid=2349494a-6adf-49f7-8096-2125a969e104',
            $response->Url
        );
        $this->assertSame(
            'https://gateway.altapaysecure.com/eCommerce.php/API/embeddedPaymentWindow?pid=2349494a-6adf-49f7-8096-2125a969e104',
            $response->DynamicJavascriptUrl
        );
    }

    public function test_language_types(): void
    {
        $this->allowedTypes(
            LanguageTypes::class,
            'language',
            'setLanguage'
        );
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        $config = new Config();
        $config
            ->setCallbackForm(sprintf('%s/%s', self::CONFIG_URL, 'form'))
            ->setCallbackOk(sprintf('%s/%s', self::CONFIG_URL, 'ok'))
            ->setCallbackFail(sprintf('%s/%s', self::CONFIG_URL, 'fail'))
            ->setCallbackRedirect(sprintf('%s/%s', self::CONFIG_URL, 'redirect'))
            ->setCallbackOpen(sprintf('%s/%s', self::CONFIG_URL, 'open'))
            ->setCallbackNotification(sprintf('%s/%s', self::CONFIG_URL, 'notification'))
            ->setCallbackVerifyOrder(sprintf('%s/%s', self::CONFIG_URL, 'verify'))
            ->setCallbackMobileAppRedirect(sprintf('%s/%s', self::CONFIG_URL, 'mobile_app_redirect'));

        return $config;
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
            $api->setAmount(200.50);
            $api->setCurrency(957);
            $api->setShopOrderId('order id');
            $api->setTerminal('my terminal');
            $api->{$setter}($type);
            $api->call();
            $request = $api->getRawRequest();
            parse_str($request->getUri()->getQuery(), $parts);
            parse_str($request->getBody()->getContents(), $parts);
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
        $api->setAmount(200.50);
        $api->setCurrency(957);
        $api->setShopOrderId('order id');
        $api->setTerminal('my terminal');
        $api->{$method}($type);
        $api->call();
        $this->assertFalse($class::isAllowed($type));
    }
}

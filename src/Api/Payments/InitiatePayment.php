<?php
/**
 * Copyright (c) 2024 AltaPay
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Altapay\Api\Payments;

use Altapay\AbstractApi;
use Altapay\Exceptions\ClientException;
use Altapay\Exceptions\ResponseHeaderException;
use Altapay\Exceptions\ResponseMessageException;
use Altapay\Request\Card;
use Altapay\Response\InitiatePaymentResponse;
use Altapay\Response\PaymentRequestResponse;
use Altapay\Serializer\ResponseSerializer;
use Altapay\Traits;
use Altapay\Types;
use GuzzleHttp\Exception\ClientException as GuzzleHttpClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This will initiate a credit card payment.
 */
class InitiatePayment extends AbstractApi
{
    use Traits\AmountTrait;
    use Traits\TerminalTrait;
    use Traits\CurrencyTrait;
    use Traits\ShopOrderIdTrait;
    use Traits\TransactionInfoTrait;
    use Traits\CustomerInfoTrait;
    use Traits\AgreementTrait;

    /**
     * The id of the order in your web shop
     *
     * @param string $shopOrderId
     *
     * @return $this
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->unresolvedOptions['shop_orderid'] = $shopOrderId;

        return $this;
    }

    /**
     * Set the credit card
     *
     * @param Card $card
     *
     * @return $this
     */
    public function setCard(Card $card)
    {
        $this->unresolvedOptions['cardnum'] = $card->getCardNumber();
        $this->unresolvedOptions['emonth']  = $card->getExpiryMonth();
        $this->unresolvedOptions['eyear']   = $card->getExpiryYear();
        $this->unresolvedOptions['cvc']     = $card->getCvc();

        return $this;
    }

    /**
     * @param array<string> $agreement
     *
     * @return $this
     */
    public function setAgreement($agreement)
    {
        $this->unresolvedOptions['agreement'] = $agreement;

        return $this;
    }
    /**
     * A credit card token previously received from an eCommerce payment or an other MO/TO payment.
     *
     * @param string $token A credit card token previously received from an eCommerce payment or an other MO/TO payment.
     * @param string $cvc   The CVC/CVV/CVV2/Security Code
     *
     * @return $this
     */
    public function setCreditCardToken($token, $cvc = null)
    {
        $this->unresolvedOptions['credit_card_token'] = $token;
        if ($cvc) {
            $this->unresolvedOptions['cvc'] = $cvc;
        }

        return $this;
    }

    /**
     * The type of payment
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->unresolvedOptions['type'] = $type;

        return $this;
    }

    /**
     * If you wish to define the reconciliation identifier used in the reconciliation csv files
     *
     * @param string $identifier
     *
     * @return $this
     */
    public function setSaleReconciliationIdentifier($identifier)
    {
        $this->unresolvedOptions['sale_reconciliation_identifier'] = $identifier;

        return $this;
    }

    /**
     * The source of the payment.
     *
     * @param string $paymentSource
     *
     * @return $this
     */
    public function setPaymentSource($paymentSource)
    {
        $this->unresolvedOptions['payment_source'] = $paymentSource;

        return $this;
    }

    /**
     * If you wish to decide which fraud detection service to use
     *
     * @param string $fraudService
     *
     * @return $this
     */
    public function setFraudService($fraudService)
    {
        $this->unresolvedOptions['fraud_service'] = $fraudService;

        return $this;
    }

    /**
     * Fraud detection services can use this parameter in the fraud detection calculations
     *
     * @param string $shippingMethod
     *
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->unresolvedOptions['shipping_method'] = $shippingMethod;

        return $this;
    }

    /**
     * Sets the selected card scheme.
     *
     * @param string $selecteScheme
     *
     * @return $this
     */
    public function setSelectedScheme($selecteScheme)
    {
        $this->unresolvedOptions['selected_scheme'] = $selecteScheme;

        return $this;
    }

    /**
     * Configure options
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'terminal',
            'shop_orderid',
            'amount',
            'currency',
            'type',
            'payment_source'
        ]);
        $resolver->setDefault('type', 'payment');
        $resolver->setAllowedValues('type', Types\PaymentTypes::getAllowed());
        $resolver->setAllowedValues('payment_source', Types\PaymentSources::getAllowed());
        $resolver->setDefault('payment_source', 'eCommerce');

        $resolver->setDefined([
            'cardnum',
            'emonth',
            'eyear',
            'cvc',
            'credit_card_token',
            'sale_reconciliation_identifier',
            'transaction_info',
            'agreement',
            'fraud_service',
            'customer_info',
            'selected_scheme'
        ]);
        $resolver->setAllowedValues('selected_scheme', Types\SelectedSchemes::getAllowed());
        $resolver->setNormalizer('cardnum', function (Options $options, $value) {
            if (isset($options['credit_card_token'])) {
                throw new \InvalidArgumentException(
                    sprintf('You can not set both a credit card and a credit card token')
                );
            }

            return $value;
        });

        $resolver->setNormalizer('credit_card_token', function (Options $options, $value) {
            $fields = ['cardnum', 'emonth', 'eyear'];
            foreach ($fields as $field) {
                if (isset($options[$field])) {
                    throw new \InvalidArgumentException(
                        sprintf('You can not set both a credit card token and a credit card')
                    );
                }
            }

            return $value;
        });
    }

    /**
     * Handle response
     *
     * @param Request $request
     * @param ResponseInterface $response
     *
     * @return InitiatePaymentResponse
     * @throws \Exception
     */
    protected function handleResponse(Request $request, ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        $xml  = new \SimpleXMLElement($body);

        return ResponseSerializer::serialize(InitiatePaymentResponse::class, $xml->Body, $xml->Header);
    }

    /**
     * @return array<string, string>
     */
    protected function getBasicHeaders()
    {
        $headers = parent::getBasicHeaders();
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';

        return $headers;
    }

    /**
     * Url to api call
     *
     * @param array<string, mixed> $options Resolved options
     *
     * @return string
     */
    protected function getUrl(array $options)
    {
        return 'initiatePayment';
    }

    /**
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return \Altapay\Response\AbstractResponse|PaymentRequestResponse|bool|void
     *
     * @throws \Exception
     * @throws ClientException
     * @throws GuzzleException
     * @throws ResponseHeaderException
     * @throws ResponseMessageException
     */
    protected function doResponse()
    {
        $this->doConfigureOptions();
        $headers           = $this->getBasicHeaders();
        $requestParameters = [$this->getHttpMethod(), $this->parseUrl(), $headers];
        if (mb_strtolower($this->getHttpMethod()) === 'post') {
            $requestParameters[] = $this->getPostOptions();
        }

        $request       = new Request(...$requestParameters);

        $this->request = $request;
        try {
            $response       = $this->getClient()->send($request);
            $this->response = $response;

            $output = $this->handleResponse($request, $response);
            $this->validateResponse($output);

            return $output;
        } catch (GuzzleHttpClientException $e) {
            throw new ClientException($e->getMessage(), $e->getRequest(), $e->getResponse(), $e);
        }
    }

    /**
     * Parse the URL
     *
     * @return string
     */
    protected function parseUrl()
    {
        return sprintf(
            '%s/processor/%s/%s',
            rtrim($this->baseUrl ?: self::TESTBASEURL, '/'),
            self::VERSION,
            $this->getUrl($this->options)
        );
    }

    /**
     * @return string
     */
    protected function getPostOptions()
    {
        $options = $this->options;

        return http_build_query($options, '', '&');
    }
}

<?php
/**
 * Copyright (c) 2016 Martin Aarhof
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

use Altapay\Api\Ecommerce\PaymentRequest;
use Altapay\Response\CardWalletSessionResponse;
use Altapay\Serializer\ResponseSerializer;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardWalletSession extends PaymentRequest
{
    /**
     * @param string $url The validation URL from the Apple Pay event
     *
     * @return $this
     */
    public function setValidationUrl($url)
    {
        $this->unresolvedOptions['validationUrl'] = $url;

        return $this;
    }

    /**
     * @param string $domain The domain initializing the request
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->unresolvedOptions['domain'] = $domain;

        return $this;
    }

    /**
     * @param array<string, string> $applePayRequestData
     *
     * @return $this
     */
    public function setApplePayRequestData(array $applePayRequestData)
    {
        $this->unresolvedOptions['applePayRequestData'] = $applePayRequestData;

        return $this;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    protected function setupRequirements(OptionsResolver $resolver)
    {
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
        parent::configureOptions($resolver);

        $resolver->setDefined([
            'terminal',
            'shop_orderid',
            'amount',
            'currency',
            'validationUrl',
            'domain',
            'applePayRequestData'
        ]);

        $resolver->addAllowedTypes('validationUrl', 'string');
        $resolver->addAllowedTypes('domain', 'string');
        $resolver->addAllowedTypes('applePayRequestData', 'array'); // validationUrl, domain, source
    }

    /**
     * Handle response
     *
     * @param Request           $request
     * @param ResponseInterface $response
     *
     * @return CardWalletSessionResponse
     * @throws \Exception
     */
    protected function handleResponse(Request $request, ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        $xml  = new \SimpleXMLElement($body);

        return ResponseSerializer::serialize(CardWalletSessionResponse::class, $xml->Body, $xml->Header);
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
        return 'cardWallet/session';
    }
}

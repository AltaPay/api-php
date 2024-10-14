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

use Altapay\AbstractApi;
use Altapay\Exceptions\ClientException;
use Altapay\Exceptions\ResponseHeaderException;
use Altapay\Exceptions\ResponseMessageException;
use Altapay\Response\VerifyThreeDSecureResponse;
use Altapay\Serializer\ResponseSerializer;
use GuzzleHttp\Exception\ClientException as GuzzleHttpClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * When the funds of a payment has been reserved and the goods are ready for delivery
 * your system should capture the payment.
 *
 * By default, auto reauth is enabled for all terminals (but is only supported by a few acquirers),
 * which means if the capture fails the system will automatically try to reauth the payment and then capture again.
 * Reauthed payments, however, do not have cvv or 3d-secure protection, which means the
 * protection against chargebacks is not as good.
 * If you wish to disable auto reauth for one or more of your terminals please contact Altapay.
 */
class VerifyThreeDSecure extends AbstractApi
{
    /**
     * @param string $transactionId
     * @return VerifyThreeDSecure
     */
    public function setThreeDSTransaction($transactionId)
    {
        $this->unresolvedOptions['transactionId'] = $transactionId;
        return $this;
    }

    /**
     * @param array $threeDSecureData
     * @return $this
     */
    public function setThreeDSecureV2(array $threeDSecureData)
    {
        $this->unresolvedOptions['3DSecureV2'] = $threeDSecureData;
        return $this;
    }

    /**
     * @param array $threeDSecureData
     * @return $this
     */
    public function setThreeDSecureNets(array $threeDSecureData)
    {
        $this->unresolvedOptions['3DSecureNets'] = $threeDSecureData;
        return $this;
    }

    /**
     * @param array $threeDSecureData
     * @return $this
     */
    public function setThreeDSecureRegular(array $threeDSecureData)
    {
        $this->unresolvedOptions['3DSecureRegular'] = $threeDSecureData;
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
        $resolver->setRequired('transactionId');
        $resolver->setDefined([
            '3DSecureV2',
            '3DSecureNets',
            '3DSecureRegular'
        ]);
        $resolver->addAllowedTypes('transactionId', ['string', 'int']);
        $resolver->setAllowedTypes('3DSecureV2', 'array');
        $resolver->setAllowedTypes('3DSecureNets', 'array');
        $resolver->setAllowedTypes('3DSecureRegular', 'array');
    }

    /**
     * Handle response
     *
     * @param Request $request
     * @param ResponseInterface $response
     *
     * @return VerifyThreeDSecureResponse
     * @throws \Exception
     */
    protected function handleResponse(Request $request, ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        $xml = new \SimpleXMLElement($body);
        if ((string)$xml->Body->Result === 'Error') {
            throw new \Exception($xml->Body->MerchantErrorMessage);
        }

        try {
            return ResponseSerializer::serialize(VerifyThreeDSecureResponse::class, $xml->Body, $xml->Header);
        } catch (\Exception $e) {
            throw $e;
        }
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
        return 'verify3dSecure';
    }

    /**
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * Generate the response
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
        $headers = $this->getBasicHeaders();
        $requestParameters = [$this->getHttpMethod(), $this->parseUrl(), $headers];
        $requestParameters[] = $this->getPostOptions();

        $request = new Request(...$requestParameters);
        $this->request = $request;
        try {
            $response = $this->getClient()->send($request);
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

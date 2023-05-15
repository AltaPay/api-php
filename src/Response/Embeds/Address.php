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

namespace Altapay\Response\Embeds;

use Altapay\Response\AbstractResponse;

class Address extends AbstractResponse
{
    /**
     * @var string|null
     */
    public $email;
    
    /**
     * @var string|null
     */
    public $firstname;

    /**
     * @var string|null
     */
    public $lastname;

    /**
     * @var string|null
     */
    public $address;

    /**
     * @var string|null
     */
    public $city;

    /**
     * @var string|null
     */
    public $postalCode;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string|null
     */
    public $country;

    /**
     * @var string
     */
    public $billingAddress;

    /**
     * @var string
     */
    public $paymentMethod;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $orderAmount;

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstname = $firstName;

        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastname = $lastName;

        return $this;
    }
    
    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
    
    /**
     * Set the address.
     *
     * @param string|null $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Set the city.
     *
     * @param string|null $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }
    
    /**
     * Set the postal code.
     *
     * @param string|null $postalCode
     * @return $this
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }
    
    /**
     * Set the region.
     *
     * @param string $region
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Set the country.
     *
     * @param string|null $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
    
    /**
     * Set the billing address.
     *
     * @param string $billingAddress
     * @return $this
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }
    
    /**
     * Set the payment method.
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
    
    /**
     * Set the currency.
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }
    
    /**
     * Set the order amount.
     *
     * @param string $orderAmount
     * @return $this
     */
    public function setOrderAmount($orderAmount)
    {
        $this->orderAmount = $orderAmount;
        return $this;
    }
}

<?php

namespace Altapay\ApiTest\Functional;

use Altapay\Authentication;
use Altapay\Request\Card;
use Altapay\ApiTest\AbstractTest;

abstract class AbstractFunctionalTest extends AbstractTest
{
    const VALID_VISA_CARD_NUMBER = '4140000000001466';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        if (!file_exists(__DIR__ . '/../../.env.php')) {
            $this->markTestSkipped(
                'Can not test functional because .env.php file does not exists'
            );
        }
    }

    /**
     * @return Authentication
     */
    protected function getAuth()
    {
        $username = $_ENV['USERNAME'] ?? null;
        $password = $_ENV['PASSWORD'] ?? null;

        return new Authentication(is_string($username) ? $username : '', is_string($password) ? $password : '', $this->getBaseUrl());
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        $baseUrl = $_ENV['BASEURL'] ?? null;

        return is_string($baseUrl) ? $baseUrl : '';
    }

    /**
     * @return string
     */
    protected function getTerminal()
    {
        $terminal = $_ENV['TERMINAL'] ?? null;

        return is_string($terminal) ? $terminal : '';
    }

    /**
     * @return Card
     */
    protected function getValidCard()
    {
        return $this->generateCard(self::VALID_VISA_CARD_NUMBER);
    }

    /**
     * @param string $number
     *
     * @return Card
     */
    protected function generateCard($number)
    {
        return new Card(
            $number,
            (new \DateTime())->format('m'),
            (new \DateTime())->add(new \DateInterval('P1Y'))->format('Y'),
            '123'
        );
    }
}

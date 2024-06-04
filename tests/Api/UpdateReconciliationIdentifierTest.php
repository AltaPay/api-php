<?php

namespace Altapay\ApiTest\Api;

use Altapay\Api\Others\UpdateReconciliationIdentifier;
use Altapay\Response\UpdateReconciliationIdentifierResponse;

class UpdateReconciliationIdentifierTest extends AbstractApiTest
{
    /**
     * @return UpdateReconciliationIdentifier
     */
    protected function getReconciliationIdentifier()
    {
        $client = $this->getXmlClient(__DIR__ . '/Results/updatereconciliationidentifier.xml');

        return (new UpdateReconciliationIdentifier($this->getAuth()))
            ->setClient($client);
    }

    public function test_update_reconciliation_identifier(): void
    {
        $api = $this->getReconciliationIdentifier();
        $api->setCurrentMerchantReconciliationIdentifier('444');
        $api->setNewMerchantReconciliationIdentifier('555');
        $api->setPaymentId('5e8ed5c2-7cfd-430e-b6bd-7eff65bc2e2c');
        $this->assertInstanceOf(UpdateReconciliationIdentifierResponse::class, $api->call());
    }

    /**
     * @depends test_update_reconciliation_identifier
     */
    public function test_update_reconciliation_identifier_data(): void
    {
        $api = $this->getReconciliationIdentifier();
        $api->setCurrentMerchantReconciliationIdentifier('444');
        $api->setNewMerchantReconciliationIdentifier('555');
        $api->setPaymentId('5e8ed5c2-7cfd-430e-b6bd-7eff65bc2e2c');
        $response = $api->call();
        $this->assertInstanceOf(UpdateReconciliationIdentifierResponse::class, $response);
        $this->assertSame('Success', $response->Result);
    }
}

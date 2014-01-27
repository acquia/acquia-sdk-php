<?php

namespace Acquia\Test\Network;

use Acquia\Common\Services;
use Acquia\Network\AcquiaNetworkClient;

class AcquiaNetworkClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string|null $responseFile
     * @param int $responseCode
     *
     * @return \Acquia\Cloud\Api\CloudApiClient
     */
    public function getAcquiaNetworkClient($responseFile = null, $responseCode = 200)
    {
        $network = AcquiaNetworkClient::factory(array(
            'base_url'    => 'http://example.acquia.com/xmlrpc.php',
            'network_id'  => 'test-id',
            'network_key' => 'test-key',
        ));

        if ($responseFile !== null) {
            $this->addMockResponse($network, $responseFile, $responseCode);
        }

        return $network;
    }

    /**
     * @param \Acquia\Network\AcquiaNetworkClient $network
     * @param string $responseFile
     */
    public function addMockResponse(AcquiaNetworkClient $network, $responseFile, $responseCode)
    {
        $mock = new \Guzzle\Plugin\Mock\MockPlugin();

        $response = new \Guzzle\Http\Message\Response($responseCode);
        if (is_string($responseFile)) {
            $response->setBody(file_get_contents($responseFile));
        }

        $mock->addResponse($response);
        $network->addSubscriber($mock);
    }

    public function testGetNetworkId()
    {
        $this->assertEquals('test-id', $this->getAcquiaNetworkClient()->getNetworkId());
    }

    public function testGetNetworkKey()
    {
        $this->assertEquals('test-key', $this->getAcquiaNetworkClient()->getNetworkKey());
    }

    public function testGetBuilderParams()
    {
        $expected = array(
            'base_url'    => 'http://example.acquia.com/xmlrpc.php',
            'network_id'  => 'test-id',
            'network_key' => 'test-key',
        );
        $this->assertEquals($expected, $this->getAcquiaNetworkClient()->getBuilderParams());
    }

    public function testCallValidCredentials()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/validate_credentials.xml');
        $this->assertTrue($network->validateCredentials());
    }

    public function testCallValidCredentialsInvalid()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/validate_credentials_invalid.xml');
        $this->assertFalse($network->validateCredentials());
    }

    public function testCallGetSubscriptionName()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_name.xml');
        $this->assertEquals($network->getSubscriptionName(), 'test subcription');
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testCallGetSubscriptionNameMissingKey()
    {
        $network = AcquiaNetworkClient::factory(array(
            'base_url'    => 'http://example.acquia.com/xmlrpc.php',
        ));
        $network->getSubscriptionName();
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testCallGetSubscriptionNameInvalidResponse()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_name_invalid.xml');
        $network->getSubscriptionName();
    }

    public function testCallGetCommunicationSettings()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/communication_settings.xml');
        $settings = $network->getCommunicationSettings('email');
        $this->assertEquals($settings['hash_setting'], '$S$foo');
    }

    public function testCallGetSubscriptionCredentials()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_credentials.xml');
        $credentials = $network->getSubscriptionCredentials('email', 'pass');
        $this->assertEquals($credentials['identifier'], 'test-id');
    }

    public function testCallSubscriptionActive()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_active.xml');
        $this->assertTrue($network->subscriptionActive());
    }

    public function testCallSubscriptionActiveInactive()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_active_inactive.xml');
        $this->assertFalse($network->subscriptionActive());
    }

    public function testCallCheckSubscription()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/check_subscription.xml');
        $subscription = $network->checkSubscription(Services::ACQUIA_SEARCH);

        $this->assertInstanceOf('\Acquia\Network\Subscription', $subscription);
        $this->assertEquals('xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx', (string) $subscription);

        $this->assertTrue($subscription->isActive());
        $this->assertEquals('test-id', $subscription->getId());
        $this->assertEquals('test-key', $subscription->getKey());
        $this->assertEquals('xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx', $subscription->getUuid());
        $this->assertEquals('https://insight.acquia.com/node/uuid/xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx/dashboard', $subscription->getDashboardUrl());
        $this->assertInstanceOf('\DateTime', $subscription->getExpirationDate());
        $this->assertEquals('Acquia Network', $subscription->getProductName());
    }
}

<?php

namespace Acquia\Test\Common;

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

    public function testMockValidCredentials()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/valid_credentials.xml');
        $this->assertTrue($network->validateCredentials());
    }

    public function testMockGetSubscriptionName()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_name.xml');
        $this->assertEquals($network->getSubscriptionName(), 'test subcription');
    }

    public function testGetCommunicationSettings()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/communication_settings.xml');
        $settings = $network->getCommunicationSettings('email');
        $this->assertEquals($settings['hash_setting'], '$S$foo');
    }

    public function testGetSubscriptionCredentials()
    {
        $network = $this->getAcquiaNetworkClient(__DIR__ . '/xml/subscription_credentials.xml');
        $credentials = $network->getSubscriptionCredentials('email', 'pass');
        $this->assertEquals($credentials['identifier'], 'test-id');
    }
}

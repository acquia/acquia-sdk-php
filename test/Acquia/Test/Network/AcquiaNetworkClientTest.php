<?php

namespace Acquia\Test\Common;

use Acquia\Network\AcquiaNetworkClient;
use Guzzle\Http\Message\Header;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class AcquiaNetworkClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Acquia\Network\AcquiaNetworkClient
     * @param string $responseData
     */
    public function addMockResponse(AcquiaNetworkClient $client, $responseData)
    {
        $mock = new MockPlugin();

        $response = new Response(200);

        $response->setBody($this->mockXmlrpcResponse($responseData));

        $mock->addResponse($response);
        $client->addSubscriber($mock);
    }

    public function mockXmlrpcResponse($value)
    {
        $response = "<?xml version=\"1.0\"?>

<methodResponse>
  <params>
  <param>
    <value>$value</value>
  </param>
  </params>
</methodResponse>
        ";
        return $response;
    }

    public function getAcquiaNetworkClient()
    {
        return AcquiaNetworkClient::factory(array(
            'network_id' => 'test-id',
            'network_key' => 'test-key',
        ));
    }

    public function testMockValidCredentials()
    {
        $response = '<boolean>1</boolean>';
        $client = $this->getAcquiaNetworkClient();
        $this->addMockResponse($client, $response);

        $this->assertTrue($client->validateCredentials());
    }


    public function testMockGetSubscriptionName()
    {
        $response = '<struct>
  <member><name>is_error</name><value><boolean>0</boolean></value></member>
  <member><name>body</name><value><struct>
  <member><name>subscription</name><value><struct>
  <member><name>site_name</name><value><string>test subcription</string></value></member>
</struct></value></member>
</struct></value></member>
</struct>';

        $client = $this->getAcquiaNetworkClient();
        $this->addMockResponse($client, $response);

        $this->assertEquals($client->getSubscriptionName(), 'test subcription');

        $response = '<struct>
  <member><name>is_error</name><value><boolean>0</boolean></value></member>
  <member><name>body</name><value><struct>
  <member><name>subscription</name><value><struct>
  <member><name>site_name</name><value><string>foo subcription</string></value></member>
</struct></value></member>
</struct></value></member>
</struct>';

        $this->addMockResponse($client, $response);
        $this->assertNotEquals($client->getSubscriptionName(), 'test subcription');
    }

    public function testMockGetCommunicationSettings()
    {
        $response = '<struct>
  <member><name>algorithm</name><value><string>sha512</string></value></member>
  <member><name>hash_setting</name><value><string>$S$foo</string></value></member>
  <member><name>extra_md5</name><value><boolean>0</boolean></value></member>
</struct>';

        $client = $this->getAcquiaNetworkClient();
        $this->addMockResponse($client, $response);

        $settings = $client->getCommunicationSettings('email');
        $this->assertEquals($settings['hash_setting'], '$S$foo');

    }

    public function testMockGetSubscriptionCredentials()
    {
        $response = '<struct>
  <member><name>identifier</name><value><string>test-id</string></value></member>
  <member><name>key</name><value><string>test-key</string></value></member>
</struct>';

        $client = $this->getAcquiaNetworkClient();
        $this->addMockResponse($client, $response);

        $credentials = $client->getSubscriptionCredentials('email', 'pass');
        $this->assertEquals($credentials['identifier'], 'test-id');

    }
}

<?php

namespace Acquia\Test\Search;

use Acquia\Search\AcquiaSearchClient;
use Acquia\Search\AcquiaSearchAuthPlugin;
use Acquia\Search\Signature;

class AcquiaSearchAuthPluginTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Signature::setDefaultNoncerClass('Acquia\Test\Search\StaticStringNoncer');
        parent::setUp();
    }

    public function tearDown()
    {
        Signature::setDefaultNoncerClass('Acquia\Rest\RandomStringNoncer');
        parent::tearDown();
    }

    /**
     * @return \Acquia\Search\AcquiaSearchAuthPlugin
     */
    public function getAuthPlugin()
    {
        $signature = new Signature('testkey');
        return new AcquiaSearchAuthPlugin('testid', $signature);
    }

    public function testGetters()
    {
        $plugin = $this->getAuthPlugin();
        $this->assertEquals('testid', $plugin->getIndexId());
        $this->assertEquals('testkey', $plugin->getDerivedKey());
    }

    public function testSetters()
    {
        $plugin = $this->getAuthPlugin();
        $plugin->setIndexId('anotherid');
        $this->assertEquals('anotherid', $plugin->getIndexId());
    }

    public function testGetHash()
    {
        $indexId = 'ABCD-12345';
        $derivedKey = 'secretKey';

        $index = AcquiaSearchClient::factory(array(
            'index_id' => $indexId,
            'derived_key' => $derivedKey,
        ));

        $request = $index->get('/select?wt=json&q=test');

        $signature = new Signature($derivedKey);
        $signature->setRequestTime(12345);

        $auth = new AcquiaSearchAuthPlugin($indexId, $signature);

        $auth->signRequest($request);
        $cookie = $request->getHeader('Cookie');

        $expected = 'acquia_solr_time=12345; acquia_solr_nonce=12345678; acquia_solr_hmac=2f0a6778df98537117ad386ea0dff64972948813;';
        $this->assertEquals($expected, (string) $cookie);
    }

    public function testPostHash()
    {
        $indexId = 'ABCD-12345';
        $derivedKey = 'secretKey';

        $index = AcquiaSearchClient::factory(array(
            'index_id' => $indexId,
            'derived_key' => $derivedKey,
        ));

        $request = $index->post('/update?wt=json', null, 'Some Body');

        $signature = new Signature($derivedKey);
        $signature->setRequestTime(12345);

        $auth = new AcquiaSearchAuthPlugin($indexId, $signature);

        $auth->signRequest($request);
        $cookie = $request->getHeader('Cookie');

        $expected = 'acquia_solr_time=12345; acquia_solr_nonce=12345678; acquia_solr_hmac=e006724d186805fdc0999cd1b132a9f78786b7f4;';
        $this->assertEquals($expected, (string) $cookie);
    }
}

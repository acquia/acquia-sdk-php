<?php

namespace Acquia\Test\Common;

use Acquia\Common\AcquiaServiceManager;
use Acquia\Common\Json;
use Guzzle\Service\Builder\ServiceBuilder;

class AcquiaServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        file_put_contents('build/test/testgroup.json', Json::encode(array(
            'services' => array(
                'testservice' => array(
                    'class' => 'Acquia\Test\Common\DummyClient',
                    'params' => array(
                        'param1' => 'foo',
                        'param2' => 'bar',
                    ),
                ),
            ),
        )));
    }

    public function tearDown()
    {
        parent::tearDown();
        unlink('build/test/testgroup.json');
    }

    public function getAcquiaServiceManager()
    {
        return new AcquiaServiceManager(array(
            'conf_dir' => 'build/test',
        ));
    }

    public function testConfigDefaults()
    {
        $service = new AcquiaServiceManager();

        $expected = 'conf/testgroup.json';
        $filename = $service->getConfigFilename('testgroup');
        $this->assertEquals($expected, $filename);
    }

    public function testConfigFilename()
    {
        $service = new AcquiaServiceManager(array(
            'conf_dir' => 'test-dir',
        ));

        $expected = 'test-dir/testgroup.json';
        $filename = $service->getConfigFilename('testgroup');

        $this->assertEquals($expected, $filename);
    }

    public function testHasConfigFile()
    {
        $service = $this->getAcquiaServiceManager();

        $this->assertTrue($service->hasConfigFile('testgroup'));
        $this->assertFalse($service->hasConfigFile('missing'));
    }

    public function testLoad()
    {
        $service = $this->getAcquiaServiceManager();

        $testBuilder = $service['testgroup'];
        $this->assertTrue(isset($testBuilder['testservice']));
        $this->assertTrue($testBuilder['testservice'] instanceof DummyClient);

        // the getBuilder() method is the same as offsetGet().
        $sameBuilder = $service->getBuilder('testgroup');
        $this->assertEquals($sameBuilder, $testBuilder);

        // A non-existent service should return an empty builder.
        $missingBuilder = $service['missing'];
        $this->assertFalse(isset($missingBuilder['testservice']));
    }

    public function testSetBuilder()
    {
        $service = new AcquiaServiceManager();

        $builder = ServiceBuilder::factory(array());
        $service->setBuilder('newbuilder', $builder);

        $this->assertEquals($builder, $service->getBuilder('newbuilder'));
    }

    public function testRemoveBuilder()
    {
        $service = $this->getAcquiaServiceManager();

        $builder = $service['testgroup'];
        unset($service['testgroup']);
        $this->assertEmpty(isset($service['testgroup']));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testSetInvalidBuilder()
    {
        $service = new AcquiaServiceManager();

        $builder = 'Not a ServiceBuilder class';
        $service['invalid'] = $builder;
    }

    public function testSetClient()
    {
        $service = $this->getAcquiaServiceManager();

        $client = new DummyClient();
        $service->setClient('testgroup', 'newservice', $client);
        $this->assertEquals($client, $service->getClient('testgroup', 'newservice'));
    }

    public function testGetClient()
    {
        $service = $this->getAcquiaServiceManager();

        $testService = $service->getClient('testgroup', 'testservice');
        $this->assertTrue($testService instanceof DummyClient);

        $missingService = $service->getClient('testgroup', 'missing');
        $this->assertNull($missingService);
    }

    public function testRemoveClient()
    {
        $service = $this->getAcquiaServiceManager();

        $service->removeClient('testgroup', 'testservice');
        $service->save();

        $json = file_get_contents('build/test/testgroup.json');
        $data = Json::decode($json);

        $this->assertEmpty($data['services']);
    }
}

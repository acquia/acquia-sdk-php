<?php

namespace Acquia\Test\Common;

use Acquia\Common\AcquiaServiceManager;
use Acquia\Json\Json;
use Guzzle\Service\Builder\ServiceBuilder;

class AcquiaServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $builderConfig = array(
        'services' => array(
            'testservice' => array(
                'class' => 'Acquia\Test\Common\DummyClient',
                'params' => array(
                    'param1' => 'foo',
                    'param2' => 'bar',
                ),
            ),
        ),
    );

    public function setUp()
    {
        parent::setUp();

        if (!is_dir('build/test')) {
            mkdir('build/test', 0755, true);
        }

        file_put_contents('build/test/testgroup.json', Json::encode($this->builderConfig));
    }

    public function tearDown()
    {
        parent::tearDown();
        if (file_exists('build/test/testgroup.json')) {
            unlink('build/test/testgroup.json');
        }
    }

    public function getAcquiaServiceManager()
    {
        return new AcquiaServiceManager(array(
            'conf_dir' => 'build/test',
        ));
    }

    public function testGetConfig()
    {
        $services = $this->getAcquiaServiceManager();
        $this->assertEquals('build/test', $services->getConfig()->get('conf_dir'));
    }

    public function testSetFilesystem()
    {
        $filesystem = new MockFilesystem();
        $services = $this->getAcquiaServiceManager();
        $services->setFilesystem($filesystem);

        $this->assertEquals($filesystem, $services->getFilesystem());
    }

    public function testGetDefaultFilesystem()
    {
        $services = $this->getAcquiaServiceManager();
        $this->assertInstanceOf('\Symfony\Component\Filesystem\Filesystem', $services->getFilesystem());
    }

    public function testConfigDefaults()
    {
        $services = new AcquiaServiceManager();

        $expected = 'conf/testgroup.json';
        $filename = $services->getConfigFilename('testgroup');
        $this->assertEquals($expected, $filename);
    }

    public function testConfigFilename()
    {
        $services = new AcquiaServiceManager(array(
            'conf_dir' => 'test-dir',
        ));

        $expected = 'test-dir/testgroup.json';
        $filename = $services->getConfigFilename('testgroup');

        $this->assertEquals($expected, $filename);
    }

    public function testHasConfigFile()
    {
        $services = $this->getAcquiaServiceManager();

        $this->assertTrue($services->hasConfigFile('testgroup'));
        $this->assertFalse($services->hasConfigFile('missing'));
    }

    public function testLoad()
    {
        $services = $this->getAcquiaServiceManager();

        $testBuilder = $services['testgroup'];
        $this->assertTrue(isset($testBuilder['testservice']));
        $this->assertTrue($testBuilder['testservice'] instanceof DummyClient);

        // the getBuilder() method is the same as offsetGet().
        $sameBuilder = $services->getBuilder('testgroup');
        $this->assertEquals($sameBuilder, $testBuilder);

        // A non-existent service should return an empty builder.
        $missingBuilder = $services['missing'];
        $this->assertFalse(isset($missingBuilder['testservice']));
    }

    public function testSetBuilder()
    {
        $services = new AcquiaServiceManager();

        $builder = ServiceBuilder::factory(array());
        $services->setBuilder('newbuilder', $builder);

        $this->assertEquals($builder, $services->getBuilder('newbuilder'));
    }

    public function testRemoveBuilder()
    {
        $services = $this->getAcquiaServiceManager();

        $builder = $services['testgroup'];
        unset($services['testgroup']);
        $this->assertEmpty(isset($services['testgroup']));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testSetInvalidBuilder()
    {
        $services = new AcquiaServiceManager();

        $builder = 'Not a ServiceBuilder class';
        $services['invalid'] = $builder;
    }

    public function testSetClient()
    {
        $services = $this->getAcquiaServiceManager();

        $client = new DummyClient();
        $services->setClient('testgroup', 'newservice', $client);
        $this->assertEquals($client, $services->getClient('testgroup', 'newservice'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testSetInvalidClient()
    {
        $services = $this->getAcquiaServiceManager();

        $badClient = new DummyInvalidClient();
        $services->setClient('testgroup', 'newservice', $badClient);
    }

    public function testGetClient()
    {
        $services = $this->getAcquiaServiceManager();

        $testService = $services->getClient('testgroup', 'testservice');
        $this->assertTrue($testService instanceof DummyClient);

        $missingService = $services->getClient('testgroup', 'missing');
        $this->assertNull($missingService);
    }

    public function testRemoveClient()
    {
        $services = $this->getAcquiaServiceManager();

        $services->removeClient('testgroup', 'testservice');
        $services->save();

        $json = file_get_contents('build/test/testgroup.json');
        $data = Json::decode($json);

        $this->assertEmpty($data['services']);
    }

    public function testSaveNewServiceGroup()
    {
        $services = $this->getAcquiaServiceManager();
        $builder = $services->getBuilder('testgroup');
        $services->setBuilder('newgroup', clone $builder);

        $services->save();
        $this->assertFileExists('build/test/newgroup.json');

        $services->deleteServiceGroup('newgroup');
        $this->assertFileNotExists('build/test/newgroup.json');
    }

    public function testDeleteServiceGroup()
    {
        $services = $this->getAcquiaServiceManager();
        $builder = $services->getBuilder('testgroup');
        $services->deleteServiceGroup('testgroup');

        $this->assertFileNotExists('build/test/testgroup.json');
        $this->assertFalse(isset($services['testgroup']));
    }
}

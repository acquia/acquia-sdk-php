<?php

namespace Acquia\Test\Cloud\Api;

use Acquia\Cloud\Api\Response as CloudResponse;
use Acquia\Cloud\Api\CloudApiClient;
use Acquia\Cloud\Api\CloudApiAuthPlugin;
use Acquia\Common\Json;
use Guzzle\Http\Message\Header;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Common\Event;

class CloudApiClientTest extends \PHPUnit_Framework_TestCase
{
    const REQUEST_PATH = 'https://cloudapi.example.com/v1/sites/myhostingstage%3Amysitegroup';

    /**
     * @return \Acquia\Cloud\Api\CloudApiClient
     */
    public function getCloudApiClient()
    {
        return CloudApiClient::factory(array(
            'base_url' => 'https://cloudapi.example.com',
            'username' => 'test-username',
            'password' => 'test-password',
        ));
    }

    /**
     * Helper function that returns an environment data array
     *
     * @param string $stage dev|test|prod
     *
     * @return array
     */
    public function getEnvironmentData($stage = 'dev')
    {
        return array(
            'livedev' => 'enabled',
            'db_clusters' => array(1234),
            'ssh_host' => 'server-1.myhostingstage.hosting.example.com',
            'name' => $stage,
            'vcs_path' => ($stage == 'dev') ? 'master' : 'tags/v1.0.1',
            'default_domain' => "mysitegroup{$stage}.myhostingstage.example.com",
        );
    }

    /**
     * Helper function that returns a server data array
     *
     * @param string $type bal|web|db|free|staging|ded|vcs
     *
     * @return array
     */
    public function getServerData($type = 'web')
    {
        $number = rand(1000,9999);
        $serverName = "{$type}-{$number}";
        $serverIp = rand(1,254) . '.' . rand(1,254);

        $serverData = array(
            'services' => array(),
            'ec2_region' => 'aq-south-1',
            'ami_type' => 'c1.medium',
            'fqdn' => '{$server_name}.myhostingstage.hosting.example.com',
            'name'=> $serverName,
            'ec2_availability_zone' => 'aq-east-1z',
        );

        switch($type) {
            case 'bal':
                $serverData['services']['varnish'] = array(
                    'status' => 'active',
                );
                $serverData['services']['external_ip'] = "172.16.{$serverIp}";
                break;
            case 'web':
                $serverData['services']['web'] = array(
                    'php_max_procs' => '2',
                    'env_status' => 'active',
                    'status' => 'online',
                );
                break;
            case 'db':
                $serverData['services']['database'] = array();
                break;
            case 'free':
            case 'staging':
            case 'ded':
                $serverData['services']['web'] = array(
                    'php_max_procs' => '2',
                    'env_status' => 'active',
                    'status' => 'online',
                );
                $serverData['services']['database'] = array();
                break;
            case 'vcs':
                $serverData['services']['vcs'] = array (
                    'vcs_url' => 'mysite@vcs-1234.myhostingstage.hosting.example.com:mysite.git',
                    'vcs_type' => 'git',
                    'vcs_path' => 'master',
                );
                break;
        }

        return $serverData;
    }

    /**
     * Helper function that returns a database data array
     *
     * @param string $name
     *
     * @return array
     */
    public function getDatabaseData($name = "zero")
    {
        $instance_name = 'db' . rand();
        return array(
            "username" => "test-username",
            "password" => "test-password",
            "instance_name" => $instance_name,
            "name" => $name,
            "db_cluster" => "1234",
            "host" => 'server-1.myhostingstage.hosting.example.com'
        );
    }

    /**
     * Helper function that returns a database backup task
     *
     * @param string $date
     *
     * @return array
     */
    public function getBackupData($date = '1978-11-29') {
        return array(
            'link' => "http://mysitedev.myhostingstage.hosting.example.com/AH_DOWNLOAD?dev=123456789dea
dbeef&d=/mnt/files/dbname.dev/backups/dev-mysite-dbname-{$date}.sql.gz&t=1386777107",
            'deleted' => 0,
            'completed' => 1386657182,
            'path' => "backups/dev-mysite-dbname-{$date}.sql.gz&t=1386777107",
            'type' => 'daily',
            'checksum' => '123456789deadbeef',
            'name' => 'dbname',
            'id' => rand(10000,99999),
            'started' => 1386657182
        );
    }

    public function getTaskInfo($id = 1) {
        return array(
            'recipient' => '',
            'created' => time(),
            'body' => "{}",
            'id' => $id,
            'hidden' => 0,
            'result' => '',
            'queue' => 'site-install',
            'percentage' => '',
            'state' => 'waiting',
            'started' => '',
            'cookie' => '',
            'sender' => 'cloud_api',
            'description' => "Test task",
            'completed' => '',
        );
    }

    /**
     * Helper function that returns the event listener.
     *
     * @param \Acquia\Cloud\Api\CloudApiClient $cloudapi
     *
     * @return \Acquia\Cloud\Api\CloudApiAuthPlugin
     *
     * @throws \UnexpectedValueException
     */
    public function getRegisteredAuthPlugin(CloudApiClient $cloudapi)
    {
        $listeners = $cloudapi->getEventDispatcher()->getListeners('request.before_send');
        foreach ($listeners as $listener) {
            if (isset($listener[0]) && $listener[0] instanceof CloudApiAuthPlugin) {
                return $listener[0];
            }
        }

        throw new \UnexpectedValueException('Expecting subscriber Acquia\Cloud\Api\CloudApiAuthPlugin to be registered');
    }

    /**
     * @param \Acquia\Cloud\Api\CloudApiClient $cloudapi
     * @param array $responseData
     */
    public function addMockResponse(CloudApiClient $cloudapi, array $responseData)
    {
        $mock = new MockPlugin();

        $response = new Response(200);
        $response->setBody(Json::encode($responseData));

        $mock->addResponse($response);
        $cloudapi->addSubscriber($mock);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequireUsername()
    {
        CloudApiClient::factory(array(
            'password' => 'test-password',
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequirePassword()
    {
        CloudApiClient::factory(array(
            'username' => 'test-username',
        ));
    }

    public function testGetBuilderParams()
    {
        $expected = array (
            'base_url' => 'https://cloudapi.example.com',
            'username' => 'test-username',
            'password' => 'test-password',
        );

        $cloudapi = $this->getCloudApiClient();
        $this->assertEquals($expected, $cloudapi->getBuilderParams());
    }

    public function testGetBasePath()
    {
        $cloudapi = $this->getCloudApiClient();
        $this->assertEquals('/v1', $cloudapi->getConfig('base_path'));
    }

    public function testHasAuthPlugin()
    {
        $cloudapi = $this->getCloudApiClient();
        $hasPlugin = (boolean) $this->getRegisteredAuthPlugin($cloudapi);
        return $this->assertTrue($hasPlugin);
    }

    public function testMockCall()
    {
        $cloudapi = $this->getCloudApiClient();

        $mock = new MockPlugin();
        $mock->addResponse(new Response(200));
        $cloudapi->addSubscriber($mock);

        $request = $cloudapi->get('sites');
        $request->send();

        $header = $request->getHeader('Authorization');
        $this->assertTrue($header instanceof Header);
    }

    public function sitesCallListener(Event $e) {
        $this->assertEquals('https://cloudapi.example.com/v1/sites.json', $e['request']->getUrl());
    }

    public function testMockSitesCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array($siteName);

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'sitesCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $sites = $cloudapi->sites();
        $this->assertTrue($sites instanceof CloudResponse\Sites);
        $this->assertTrue($sites[$siteName] instanceof CloudResponse\Site);
    }

    public function siteCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '.json', $e['request']->getUrl());
    }

    public function testMockSiteCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array (
            'production_mode' => '1',
            'title' => 'My Site',
            'vcs_type' => 'git',
            'vcs_url' => 'mysitegroup@git.example.com:mysitegroup.git',
            'unix_username' => 'mysitegroup',
            'name' => $siteName,
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'siteCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $site = $cloudapi->site($siteName);
        $this->assertEquals($site['hosting_stage'], 'myhostingstage');
        $this->assertEquals($site['site_group'], 'mysitegroup');
    }

    public function environmentsCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs.json', $e['request']->getUrl());
    }

    public function testMockEnvironmentsCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array (
            $this->getEnvironmentData('dev'),
            $this->getEnvironmentData('test'),
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'environmentsCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $environments = $cloudapi->environments($siteName);
        $this->assertTrue($environments instanceof CloudResponse\Environments);
        $this->assertTrue($environments['dev'] instanceof CloudResponse\Environment);
        $this->assertTrue($environments['test'] instanceof CloudResponse\Environment);
    }

    public function environmentCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev.json', $e['request']->getUrl());
    }

    public function testMockEnvironmentCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = $this->getEnvironmentData('dev');

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'environmentCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $env = $cloudapi->environment($siteName, 'dev');
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $env[$key]);
        }
    }

    public function installDistroByNameCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/install/distro_name.json?source=acquia-drupal-7', $e['request']->getUrl());
    }

    public function testMockInstallDistroByNameCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $environment = 'dev';
        $type = 'distro_name';
        $source = 'acquia-drupal-7';

        // Response is an Acquia Cloud Task
        $responseData = array(
            'recipient' => '',
            'created' => time(),
            // The values encoded in the body can come back in any order
            'body' => sprintf('{"env":"%s","site":"%s","type":"%s","source":"%s"}', $environment, $siteName, $type, $source),
            'id' => 12345,
            'hidden' => 0,
            'result' => '',
            'queue' => 'site-install',
            'percentage' => '',
            'state' => 'waiting',
            'started' => '',
            'cookie' => '',
            'sender' => 'cloud_api',
            'description' => "Install {$source} to dev",
            'completed' => '',
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'installDistroByNameCallListener'));
        $this->addMockResponse($cloudapi, $responseData);
        $task = $cloudapi->installDistro($siteName, $environment, $type, $source);
        $this->assertTrue($task instanceof CloudResponse\Task);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $task[$key]);
        }
    }

    public function serversCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/servers.json', $e['request']->getUrl());
    }

    public function testMockServersCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array (
            $this->getServerData('bal'),
            $this->getServerData('bal'),
            $this->getEnvironmentData('free'),
            $this->getEnvironmentData('vcs'),
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'serversCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $servers = $cloudapi->servers($siteName, 'dev');
        $this->assertTrue($servers instanceof CloudResponse\Servers);
        $this->assertTrue($servers[$responseData[0]['name']] instanceof CloudResponse\Server);
        $this->assertTrue($servers[$responseData[1]['name']] instanceof CloudResponse\Server);
        $this->assertTrue($servers[$responseData[2]['name']] instanceof CloudResponse\Server);
        $this->assertTrue($servers[$responseData[3]['name']] instanceof CloudResponse\Server);
    }

    public function serverCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/servers/free.json', $e['request']->getUrl());
    }

    public function testMockServerCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = $this->getServerData('free');

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'serverCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $server = $cloudapi->server($siteName, 'dev', 'free');
        $this->assertTrue($server instanceof CloudResponse\Server);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $server[$key]);
        }
    }

    // TODO: add public function testMockMaxPhpProcsCall() {}
    // TODO: add public function testMockSshKeysCall() {}
    // TODO: add public function testMockSshKeyCall() {}
    // TODO: add public function testMockAddSshKeyCall() {}
    // TODO: add public function testMockDeleteSshKeyCall() {}
    // TODO: add public function testMockSvnUsersCall() {}
    // TODO: add public function testMockSvnUserCall() {}
    // TODO: add public function testMockAddSvnUserCall() {}
    // TODO: add public function testMockDeleteSvnUserCall() {}

    public function siteDatabasesCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/dbs.json', $e['request']->getUrl());
    }

    public function testMockSiteDatabasesCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array (
            array('name' => 'one'),
            array('name' => 'two'),
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'siteDatabasesCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $databases = $cloudapi->siteDatabases($siteName);
        $this->assertTrue($databases instanceof CloudResponse\Databases);
        $this->assertTrue($databases['one'] instanceof CloudResponse\Database);
        $this->assertTrue($databases['two'] instanceof CloudResponse\Database);
    }

    public function siteDatabaseCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/dbs/one.json', $e['request']->getUrl());
    }

    public function testMockSiteDatabaseCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = $this->getDatabaseData('one');

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'siteDatabaseCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $database = $cloudapi->siteDatabase($siteName, 'one');
        $this->assertTrue($database instanceof CloudResponse\Database);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $database[$key]);
        }
    }

    public function environmentDatabasesCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/dbs.json', $e['request']->getUrl());
    }

    public function testMockEnvironmentDatabasesCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array (
            $this->getDatabaseData('one'),
            $this->getDatabaseData('two'),
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'environmentDatabasesCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $databases = $cloudapi->environmentDatabases($siteName, 'dev');
        $this->assertTrue($databases instanceof CloudResponse\Databases);
        $this->assertTrue($databases['one'] instanceof CloudResponse\Database);
        $this->assertTrue($databases['two'] instanceof CloudResponse\Database);
    }

    public function environmentDatabaseCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/dbs/one.json', $e['request']->getUrl());
    }

    public function testMockEnvironmentDatabaseCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = $this->getDatabaseData('one');

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'environmentDatabaseCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $database = $cloudapi->environmentDatabase($siteName, 'dev', 'one');
        $this->assertTrue($database instanceof CloudResponse\Database);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $database[$key]);
        }
    }

    public function databaseBackupsCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/dbs/one/backups.json', $e['request']->getUrl());
    }

    public function testMockDatabaseBackupsCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = array(
            $this->getBackupData('2013-12-11'),
            $this->getBackupData('2013-12-10'),
            $this->getBackupData('2013-12-09')
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'databaseBackupsCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $database = $cloudapi->databaseBackups($siteName, 'dev', 'one');
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $database[$key]);
        }
    }

    public function databaseBackupCallListener(Event $e) {
        $this->assertTrue(preg_match('#^' . self::REQUEST_PATH . '/envs/dev/dbs/one/backups/[0-9]+\.json$#', $e['request']->getUrl()) > 0);
    }

    public function testMockDatabaseBackupCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $responseData = $this->getBackupData('2013-12-11');

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'databaseBackupCallListener'));
        $this->addMockResponse($cloudapi, $responseData);

        $database = $cloudapi->databaseBackup($siteName, 'dev', 'one', $responseData['id']);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $database[$key]);
        }
    }

    // TODO: add public function testMockDownloadDatabaseBackupCall() {}

    public function createDatabaseBackupCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/envs/dev/dbs/dbname/backups.json', $e['request']->getUrl());
    }

    public function testMockCreateDatabaseBackupCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $environment = 'dev';
        $type = 'distro_name';
        $source = 'acquia-drupal-7';
        $taskId = 12345;

        // Response is an Acquia Cloud Task
        $responseData = array(
            'recipient' => '',
            'created' => time(),
            // The values encoded in the body can come back in any order
            'body' => sprintf('["%s","%s","%s"]', $siteName, $environment, $siteName),
            'id' => $taskId,
            'hidden' => 0,
            'result' => '',
            'queue' => 'create-db-backup-ondemand',
            'percentage' => '',
            'state' => 'waiting',
            'started' => '',
            'cookie' => '',
            'sender' => 'cloud_api',
            'description' => "Backup database dbname in dev environment.",
            'completed' => '',
        );

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'createDatabaseBackupCallListener'));
        $this->addMockResponse($cloudapi, $responseData);
        $task = $cloudapi->createDatabaseBackup($siteName, 'dev', 'dbname');
        $this->assertTrue($task instanceof CloudResponse\Task);
        $this->assertEquals($taskId, $task['id']);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $task[$key]);
        }
    }

    public function tasksCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/tasks.json', $e['request']->getUrl());
    }

    public function testMockTasksCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $taskId = 12345;

        // Response is an Acquia Cloud Task
        $responseData = array($this->getTaskInfo($taskId), $this->getTaskInfo($taskId + 1));

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'tasksCallListener'));
        $this->addMockResponse($cloudapi, $responseData);
        $tasks = $cloudapi->tasks($siteName);

        $this->assertTrue($tasks instanceof CloudResponse\Tasks);
        $this->assertTrue($tasks[$taskId] instanceof CloudResponse\Task);
    }

    public function taskInfoCallListener(Event $e) {
        $this->assertEquals(self::REQUEST_PATH . '/tasks/12345.json', $e['request']->getUrl());
    }

    public function testMockTaskInfoCall()
    {
        $siteName = 'myhostingstage:mysitegroup';
        $taskId = 12345;

        // Response is an Acquia Cloud Task
        $responseData = $this->getTaskInfo($taskId);

        $cloudapi = $this->getCloudApiClient();
        $cloudapi->getEventDispatcher()->addListener('client.create_request', array($this, 'taskInfoCallListener'));
        $this->addMockResponse($cloudapi, $responseData);
        $task = $cloudapi->taskInfo($siteName, $taskId);
        $this->assertTrue($task instanceof CloudResponse\Task);
        $this->assertEquals($taskId, $task['id']);
        foreach($responseData as $key => $value) {
            $this->assertEquals($value, $task[$key]);
        }
    }

}

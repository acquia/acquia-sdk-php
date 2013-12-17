<?php

namespace Acquia\Cloud\Api;

use Acquia\Common\AcquiaServiceManagerAware;
use Acquia\Common\Json;
use Guzzle\Common\Collection;
use Guzzle\Service\Client;

class CloudApiClient extends Client implements AcquiaServiceManagerAware
{
    const BASE_PATH = '/v1';

    /**
     * {@inheritdoc}
     *
     * @return \Acquia\Cloud\Api\CloudApiClient
     */
    public static function factory($config = array())
    {
        $required = array(
            'base_url',
            'username',
            'password',
        );

        $defaults = array(
            'base_url' => 'https://cloudapi.acquia.com',
            'base_path' => self::BASE_PATH,
        );

        // Instantiate the Acquia Search plugin.
        $config = Collection::fromConfig($config, $defaults, $required);
        $client = new static($config->get('base_url'), $config);
        $client->setDefaultHeaders(array(
            'Content-Type' => 'application/json; charset=utf-8',
        ));

        // Attach the Acquia Search plugin to the client.
        $plugin = new CloudApiAuthPlugin($config->get('username'), $config->get('password'));
        $client->addSubscriber($plugin);

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilderParams()
    {
        return array(
            'base_url' => $this->getConfig('base_url'),
            'username' => $this->getConfig('username'),
            'password' => $this->getConfig('password'),
        );
    }

    /**
     * Helper method to send a GET request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendGet($path, $variables = array())
    {
        return $this->get(array($path, $variables))->send()->json();
    }

    /**
     * Helper method to send a GET request and save to a file.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param string $tofile
     *   Path to save file
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function saveGet($path, array $variables, $tofile)
    {
        return $this->get(array($path, $variables))->setResponseBody($tofile)->send();
    }

    /**
     * Helper method to send a POST request and return parsed JSON.
     *
     * The variables passed in the second parameter are used to expand the URI
     * expressions, which are usually the resource identifiers being requested.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param mixed $body
     *   Defaults to null. If a non-string is passed then the data is converted
     *   to JSON.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendPost($path, $variables = array(), $body = null)
    {
        if (!is_string($body)) {
            $body = Json::encode($body);
        }
        return $this->post(array($path, $variables), null, $body)->send()->json();
    }

    /**
     * Helper method to send a DELETE request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendDelete($path, $variables = array())
    {
        return $this->delete(array($path, $variables))->send()->json();
    }

    /**
     * @return \Acquia\Cloud\Api\Response\Sites
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sites()
    {
        $data = $this->sendGet('{+base_path}/sites.json');
        return new Response\Sites($data);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Site
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function site($site)
    {
        $variables = array('site' => $site);
        $data = $this->sendGet('{+base_path}/sites/{site}.json', $variables);
        return new Response\Site($data);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Environments
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environments($site)
    {
        $variables = array('site' => $site);
        $data = $this->sendGet('{+base_path}/sites/{site}/envs.json', $variables);
        return new Response\Environments($data);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return \Acquia\Cloud\Api\Response\Environment
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environment($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/envs/{env}.json', $variables);
        return new Response\Environment($data);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $type
     * @param string $source
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function installDistro($site, $env, $type, $source)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'type' => $type,
            'source' => $source,
        );
        $data = $this->sendPost('{+base_path}/sites/{site}/envs/{env}/install/{type}.json?source={source}', $variables);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return \Acquia\Cloud\Api\Response\Servers
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function servers($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/envs/{env}/servers.json', $variables);
        return new Response\Servers($data);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $server
     *
     * @return \Acquia\Cloud\Api\Response\Server
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function server($site, $env, $server)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'server' => $server,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/envs/{env}/servers/{server}.json', $variables);
        return new Response\Server($data);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $server
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function maxPhpProcs($site, $env, $server)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'server' => $server,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/servers/{server}/php-procs.json', $variables);
    }

    /**
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sshKeys($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}/sshkeys.json', $variables);
    }

    /**
     * @param string $site
     * @param int $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function sshKey($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        return $this->sendGet('{+base_path}/sites/{site}/sshkeys/{id}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $publicKey
     * @param string $nickname
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function addSshKey($site, $publicKey, $nickname)
    {
        $path = '{+base_path}/sites/{site}/sshkeys.json?nickname={nickname}';
        $variables = array(
            'site' => $site,
            'nickname' => $nickname,
        );
        $body = array('ssh_pub_key' => $publicKey);
        $data = $this->sendPost($path, $variables, $body);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     * @param int $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function deleteSshKey($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        $data = $this->sendDelete('{+base_path}/sites/{site}/sshkeys/{id}.json', $variables);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function svnUsers($site)
    {
        $variables = array('site' => $site);
        return $this->sendGet('{+base_path}/sites/{site}/svnusers.json', $variables);
    }

    /**
     * @param string $site
     * @param int $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function svnUser($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        return $this->sendGet('{+base_path}/sites/{site}/svnusers/{id}.json', $variables);
    }

    /**
     * @param string $site
     * @param string $username
     * @param string $password
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @todo Testing returned a 400 response.
     */
    public function addSvnUser($site, $username, $password)
    {
        $path = '{+base_path}/sites/{site}/svnusers/{username}.json';
        $variables = array(
            'site' => $site,
            'username' => $username,
        );
        $body = array('password' => $password);
        $data = $this->sendPost($path, $variables, $body);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     * @param int $id
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @todo Testing returned a 400 response.
     */
    public function deleteSvnUser($site, $id)
    {
        $variables = array(
            'site' => $site,
            'id' => $id,
        );
        $data = $this->sendDelete('{+base_path}/sites/{site}/svnusers/{id}.json', $variables);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Databases
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function siteDatabases($site)
    {
        $variables = array('site' => $site);
        $data = $this->sendGet('{+base_path}/sites/{site}/dbs.json', $variables);
        return new Response\Databases($data);
    }

    /**
     * @param string $site
     * @param string $db
     *
     * @return \Acquia\Cloud\Api\Response\Database
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function siteDatabase($site, $db)
    {
        $variables = array(
            'site' => $site,
            'db' => $db,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/dbs/{db}.json', $variables);
        return new Response\Database($data);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environmentDatabases($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs.json', $variables);
        return new Response\Databases($data);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function environmentDatabase($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}.json', $variables);
        return new Response\Database($data);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function databaseBackups($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        return $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups.json', $variables);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param int $id
     *
     * @return \Acquia\Cloud\Api\Response\Tasks
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function databaseBackup($site, $env, $db, $id)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $id,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}.json', $variables);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param int $id
     * @param string $outfile
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function downloadDatabaseBackup($site, $env, $db, $id, $outfile)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $id,
        );
        return $this->saveGet('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}/download.json', $variables, $outfile);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function createDatabaseBackup($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        $data = $this->sendPost('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups.json', $variables);
        return new Response\Task($data);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Tasks
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function tasks($site)
    {
        $variables = array(
            'site' => $site,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/tasks.json', $variables);
        return new Response\Tasks($data);
    }

    /**
     * @param string $site
     * @param int $task
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function taskInfo($site, $task)
    {
        $variables = array(
            'site' => $site,
            'task' => $task,
        );
        $data = $this->sendGet('{+base_path}/sites/{site}/tasks/{task}.json', $variables);
        return new Response\Task($data);
    }
}

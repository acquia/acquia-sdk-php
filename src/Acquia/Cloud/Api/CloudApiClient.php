<?php

namespace Acquia\Cloud\Api;

use Acquia\Rest\ServiceManagerAware;
use Guzzle\Common\Collection;
use Acquia\Json\Json;
use Guzzle\Service\Client;

class CloudApiClient extends Client implements ServiceManagerAware
{
    const BASE_URL         = 'https://cloudapi.acquia.com';
    const BASE_PATH        = '/v1';

    const INSTALL_MAKEFILE = 'make_url';
    const INSTALL_PROJECT  = 'distro_url';

    const LIVEDEV_ENABLE   = 'enable';
    const LIVEDEV_DISABLE  = 'disable';

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
            'base_url' => self::BASE_URL,
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
     * @return \Acquia\Cloud\Api\Response\SiteNames
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET___sites_-instance_route
     */
    public function sites()
    {
        $request = $this->get('{+base_path}/sites.json');
        return new Response\SiteNames($request);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Site
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site-instance_route
     */
    public function site($site)
    {
        $variables = array('site' => $site);
        $request = $this->get(array('{+base_path}/sites/{site}.json', $variables));
        return new Response\Site($request);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Environments
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs-instance_route
     */
    public function environments($site)
    {
        $variables = array('site' => $site);
        $request = $this->get(array('{+base_path}/sites/{site}/envs.json', $variables));
        return new Response\Environments($request);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return \Acquia\Cloud\Api\Response\Environment
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env-instance_route
     */
    public function environment($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}.json', $variables));
        return new Response\Environment($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $type
     * @param string $source
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_envs__env_install__type-instance_route
     */
    public function installDistro($site, $env, $type, $source)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'type' => $type,
            'source' => $source,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/envs/{env}/install/{type}.json?source={source}', $variables));
        return new Response\Task($request);
    }

    /**
     * Install any publicly accessible, standard Drupal distribution.
     *
     * @param string $site
     * @param string $env
     * @param string $projectName
     * @param string $version
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function installDistroByProject($site, $env, $projectName, $version)
    {
        $source = 'http://ftp.drupal.org/files/projects/' . $projectName . '-' . $version . '-core.tar.gz';
        return $this->installDistro($site, $env, self::INSTALL_PROJECT, $source);
    }

    /**
     * Install a distro by passing a URL to a Drush makefile.
     *
     * @param string $site
     * @param string $env
     * @param string $makefileUrl
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function installDistroByMakefile($site, $env, $makefileUrl)
    {
        return $this->installDistro($site, $env, self::INSTALL_MAKEFILE, $makefileUrl);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return \Acquia\Cloud\Api\Response\Servers
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_servers-instance_route
     */
    public function servers($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/servers.json', $variables));
        return new Response\Servers($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $server
     *
     * @return \Acquia\Cloud\Api\Response\Server
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_servers__server-instance_route
     */
    public function server($site, $env, $server)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'server' => $server,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/servers/{server}.json', $variables));
        return new Response\Server($request);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\SshKeys
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_sshkeys-instance_route
     */
    public function sshKeys($site)
    {
        $variables = array('site' => $site);
        $request = $this->get(array('{+base_path}/sites/{site}/sshkeys.json', $variables));
        return new Response\SshKeys($request);
    }

    /**
     * @param string $site
     * @param int $keyId
     *
     * @return \Acquia\Cloud\Api\Response\SshKey
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_sshkeys__sshkeyid-instance_route
     */
    public function sshKey($site, $keyId)
    {
        $variables = array(
            'site' => $site,
            'id' => $keyId,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/sshkeys/{id}.json', $variables));
        return new Response\SshKey($request);
    }

    /**
     * @param string $site
     * @param string $publicKey
     * @param string $nickname
     * @param bool $shellAccess
     * @param bool $vcsAccess
     * @param array $blacklist
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_sshkeys-instance_route
     */
    public function addSshKey($site, $publicKey, $nickname, $shellAccess = true, $vcsAccess = true, $blacklist = array())
    {
        $path = '{+base_path}/sites/{site}/sshkeys.json?nickname={nickname}';
        $variables = array(
            'site' => $site,
            'nickname' => $nickname,
        );
        $body = Json::encode(array(
          'ssh_pub_key' => $publicKey,
          'shell_access' => $shellAccess,
          'vcs_access' => $vcsAccess,
          'blacklist' => $blacklist,
        ));
        $request = $this->post(array($path, $variables), null, $body);
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param int $keyId
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException|\InvalidArgumentException
     *
     * @see http://cloudapi.acquia.com/#DELETE__sites__site_sshkeys__sshkeyid-instance_route
     */
    public function deleteSshKey($site, $keyId)
    {
        if (!is_string($site)) {
            throw new \InvalidArgumentException('The site parameter must be a string.');
        }
        if (!is_int($keyId)) {
            throw new \InvalidArgumentException('The keyId parameter must be an integer.');
        }
        $variables = array(
            'site' => $site,
            'id' => $keyId,
        );
        $request = $this->delete(array('{+base_path}/sites/{site}/sshkeys/{id}.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\SvnUsers
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_svnusers-instance_route
     */
    public function svnUsers($site)
    {
        $variables = array('site' => $site);
        $request = $this->get(array('{+base_path}/sites/{site}/svnusers.json', $variables));
        return new Response\SvnUsers($request);
    }

    /**
     * @param string $site
     * @param int $userId
     *
     * @return \Acquia\Cloud\Api\Response\SvnUser
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_svnusers__svnuserid-instance_route
     */
    public function svnUser($site, $userId)
    {
        $variables = array(
            'site' => $site,
            'id' => $userId,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/svnusers/{id}.json', $variables));
        return new Response\SvnUser($request);
    }

    /**
     * @param string $site
     * @param string $username
     * @param string $password
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_svnusers__username-instance_route
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
        $body = Json::encode(array('password' => $password));
        $request = $this->post(array($path, $variables), null, $body);
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param int $userId
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#DELETE__sites__site_svnusers__svnuserid-instance_route
     *
     * @todo Testing returned a 400 response.
     */
    public function deleteSvnUser($site, $userId)
    {
        $variables = array(
            'site' => $site,
            'id' => $userId,
        );
        $request = $this->delete(array('{+base_path}/sites/{site}/svnusers/{id}.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\DatabaseNames
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_dbs-instance_route
     */
    public function databases($site)
    {
        $variables = array('site' => $site);
        $request = $this->get(array('{+base_path}/sites/{site}/dbs.json', $variables));
        return new Response\DatabaseNames($request);
    }

    /**
     * @param string $site
     * @param string $db
     *
     * @return \Acquia\Cloud\Api\Response\DatabaseName
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_dbs__db-instance_route
     */
    public function database($site, $db)
    {
        $variables = array(
            'site' => $site,
            'db' => $db,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/dbs/{db}.json', $variables));
        return new Response\DatabaseName($request);
    }

    /**
     * @param string $site
     * @param string $db
     * @param array $cluster_map
     *   Optional. A mapping containing all environments and the cluster to which
     *   the associated database should be created. Each entry consists of the
     *   environment name as the key and the database cluster ID as the value.
     *   Note that if more than one cluster is associated with a site group,
     *   this map is required.
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_dbs-instance_route
     */
    public function addDatabase($site, $db, $cluster_map = NULL)
    {
        $variables = array('site' => $site);
        $options = array();
        if (is_array($cluster_map) && !empty($cluster_map)) {
            foreach ($cluster_map as $env => $db_cluster) {
                if (is_string($env) && !empty($env) &&
                  intval($db_cluster) > 0) {
                    $options[$env] = array('db_cluster' => (string) $db_cluster);
                }
            }
        }
        $body = array('db' => $db);
        if (count($options) > 0) {
            $body['options'] = $options;
        }
        $body = Json::encode($body);
        $request = $this->post(array('{+base_path}/sites/{site}/dbs.json', $variables), null, $body);
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $db
     * @param bool $backup
     *   Optional. If TRUE, a final backup of the database instance in each
     *   environment is made before deletion.
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#DELETE__sites__site_dbs__db-instance_route
     */
    public function deleteDatabase($site, $db, $backup = TRUE)
    {
        $variables = array(
            'site' => $site,
            'db' => $db,
            'backup' => $backup ? 1 : 0,
        );
        $request = $this->delete(array('{+base_path}/sites/{site}/dbs/{db}.json?backup={backup}', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return \Acquia\Cloud\Api\Response\Databases
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_dbs-instance_route
     */
    public function environmentDatabases($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/dbs.json', $variables));
        return new Response\Databases($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return \Acquia\Cloud\Api\Response\Database
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_dbs__db-instance_route
     */
    public function environmentDatabase($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}.json', $variables));
        return new Response\Database($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return \Acquia\Cloud\Api\Response\DatabaseBackups
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_dbs__db_backups-instance_route
     */
    public function databaseBackups($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups.json', $variables));
        return new Response\DatabaseBackups($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param int $backupId
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_dbs__db_backups__backup-instance_route
     */
    public function databaseBackup($site, $env, $db, $backupId)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $backupId,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}.json', $variables));
        return new Response\DatabaseBackup($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param int $backupId
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#DELETE__sites__site_envs__env_dbs__db_backups__backup-instance_route
     */
    public function deleteDatabaseBackup($site, $env, $db, $backupId)
    {
      $variables = array(
        'site' => $site,
        'env' => $env,
        'db' => $db,
        'backup' => $backupId,
      );
      $request = $this->delete(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{backup}.json', $variables));
      return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param int $backupId
     * @param string $outfile
     *
     * @return \Guzzle\Http\Message\Response
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_dbs__db_backups__backup_download-instance_route
     */
    public function downloadDatabaseBackup($site, $env, $db, $backupId, $outfile)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $backupId,
        );
        return $this
            ->get(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}/download.json', $variables))
            ->setResponseBody($outfile)
            ->send()
        ;
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_envs__env_dbs__db_backups-instance_route
     */
    public function createDatabaseBackup($site, $env, $db)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $db
     * @param string $backupId
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_envs__env_dbs__db_backups__backup_restore-instance_route
     */
    public function restoreDatabaseBackup($site, $env, $db, $backupId)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'db' => $db,
            'id' => $backupId,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/envs/{env}/dbs/{db}/backups/{id}/restore.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     *
     * @return \Acquia\Cloud\Api\Response\Tasks
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_tasks-instance_route
     */
    public function tasks($site)
    {
        $variables = array(
            'site' => $site,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/tasks.json', $variables));
        return new Response\Tasks($request);
    }

    /**
     * @param string $site
     * @param int $taskId
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_tasks__task-instance_route
     */
    public function task($site, $taskId)
    {
        $variables = array(
            'site' => $site,
            'task' => $taskId,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/tasks/{task}.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     *
     * @return \Acquia\Cloud\Api\Response\Domains
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_domains-instance_route
     */
    public function domains($site, $env)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/domains.json', $variables));
        return new Response\Domains($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $domain
     *
     * @return \Acquia\Cloud\Api\Response\Domain
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#GET__sites__site_envs__env_domains__domain-instance_route
     */
    public function domain($site, $env, $domain)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'domain' => $domain,
        );
        $request = $this->get(array('{+base_path}/sites/{site}/envs/{env}/domains/{domain}.json', $variables));
        return new Response\Domain($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $domain
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_envs__env_domains__domain-instance_route
     */
    public function addDomain($site, $env, $domain)
    {
      $variables = array(
        'site' => $site,
        'env' => $env,
        'domain' => $domain,
      );
      $request = $this->post(array('{+base_path}/sites/{site}/envs/{env}/domains/{domain}.json', $variables));
      return new Response\Task($request);
    }

    /**
     * Moves domains atomically from one environment to another.
     *
     * @param string $site
     *   The site.
     * @param string|array $domains
     *   The domain name(s) as an array of strings, or the string '*' to move all
     *   domains.
     * @param string $sourceEnv
     *   The environment which currently has this domain.
     * @param string $targetEnv
     *   The destination environment for the domain.
     * @param bool $skipSiteUpdate
     *   Optional. If set to TRUE this will inhibit running
     *   fields-config-web.php for this domain move.
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_domain_move__source__target-instance_route
     */
    public function moveDomain($site, $domains, $sourceEnv, $targetEnv, $skipSiteUpdate = FALSE)
    {
        $paths = '{+base_path}/sites/{site}/domain-move/{source}/{target}.json';
        $update_site = '';
        if ($skipSiteUpdate) {
            $update_site = '1';
            $paths .= '?skip_site_update={update_site}';
        }
        $variables = array(
          'site' => $site,
          'source' => $sourceEnv,
          'target' => $targetEnv,
          'update_site' => $update_site,
        );
        $body = Json::encode(array('domains' => (array) $domains));
        $request = $this->post(array($paths, $variables), null, $body);
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $domain
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#DELETE__sites__site_envs__env_domains__domain-instance_route
     */
    public function deleteDomain($site, $env, $domain)
    {
      $variables = array(
        'site' => $site,
        'env' => $env,
        'domain' => $domain,
      );
      $request = $this->delete(array('{+base_path}/sites/{site}/envs/{env}/domains/{domain}.json', $variables));
      return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $domain
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#DELETE__sites__site_envs__env_domains__domain_cache-instance_route
     */
    public function purgeVarnishCache($site, $env, $domain)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'domain' => $domain,
        );
        $request = $this->delete(array('{+base_path}/sites/{site}/envs/{env}/domains/{domain}/cache.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $db
     * @param string $sourceEnv
     * @param string $targetEnv
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_dbs__db_db_copy__source__target-instance_route
     */
    public function copyDatabase($site, $db, $sourceEnv, $targetEnv)
    {
        $variables = array(
            'site' => $site,
            'db' => $db,
            'source' => $sourceEnv,
            'target' => $targetEnv,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/dbs/{db}/db-copy/{source}/{target}.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $sourceEnv
     * @param string $targetEnv
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_files_copy__source__target-instance_route
     */
    public function copyFiles($site, $sourceEnv, $targetEnv)
    {
        $variables = array(
            'site' => $site,
            'source' => $sourceEnv,
            'target' => $targetEnv,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/files-copy/{source}/{target}.json', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param string $action
     * @param bool $discard
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_envs__env_livedev__action-instance_route
     */
    public function liveDev($site, $env, $action, $discard = false)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'action' => $action,
            'discard' => (int) $discard,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/envs/{env}/livedev/{action}.json?discard={discard}', $variables));
        return new Response\Task($request);
    }

    /**
     * @param string $site
     * @param string $env
     * @param bool $discard
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function enableLiveDev($site, $env, $discard = false)
    {
        return $this->liveDev($site, $env, self::LIVEDEV_ENABLE, $discard);
    }

    /**
     * @param string $site
     * @param string $env
     * @param bool $discard
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function disableLiveDev($site, $env, $discard = false)
    {
        return $this->liveDev($site, $env, self::LIVEDEV_DISABLE, $discard);
    }

    /**
     * Deploy code from on environment to another.
     *
     * @param string $site
     * @param string $sourceEnv
     * @param string $targetEnv
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_code_deploy__source__target-instance_route
     */
    public function deployCode($site, $sourceEnv, $targetEnv)
    {
        $variables = array(
            'site' => $site,
            'source' => $sourceEnv,
            'target' => $targetEnv,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/code-deploy/{source}/{target}.json', $variables));
        return new Response\Task($request);
    }

    /**
     * Deploy a tag or branch to an environment.
     *
     * @param string $site
     * @param string $env
     * @param string $vcsPath
     *
     * @return \Acquia\Cloud\Api\Response\Task
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://cloudapi.acquia.com/#POST__sites__site_envs__env_code_deploy-instance_route
     */
    public function pushCode($site, $env, $vcsPath)
    {
        $variables = array(
            'site' => $site,
            'env' => $env,
            'path' => $vcsPath,
        );
        $request = $this->post(array('{+base_path}/sites/{site}/envs/{env}/code-deploy.json?path={path}', $variables));
        return new Response\Task($request);
    }

    /**
     * @deprecated since version 0.5.0
     */
    public function taskInfo($site, $taskId)
    {
        return $this->task($site, $taskId);
    }

    /**
     * @deprecated since version 0.5.0
     */
    public function siteDatabases($site)
    {
        return $this->databases($site);
    }

    /**
     * @deprecated since version 0.5.0
     */
    public function siteDatabase($site, $db)
    {
        return $this->database($site, $db);
    }

    /**
     * @deprecated since version 0.5.0
     */
    public function codeDeploy($site, $source, $target)
    {
        return $this->deployCode($site, $source, $target);
    }
}

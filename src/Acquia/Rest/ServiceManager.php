<?php

namespace Acquia\Rest;

use Acquia\Json\Json;
use Guzzle\Service\Builder\ServiceBuilder;
use Guzzle\Service\Client;
use Symfony\Component\Filesystem\Filesystem;

class ServiceManager extends \ArrayObject
{
    /**
     * @var \Guzzle\Common\Collection
     */
    protected $config;

    /**
     * @var array
     */
    protected $removed = array();

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $defaults = array(
            'conf_dir' => 'conf',
            'conf_files' => array(),
        );

        $required = array(
            'conf_dir',
            'conf_files',
        );

        $this->config = \Guzzle\Common\Collection::fromConfig($config, $defaults, $required);
        $this->config['conf_dir'] = rtrim($this->config['conf_dir'], '/\\');
    }

    /**
     * @return \Guzzle\Common\Collection
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     *
     * @return \Acquia\Rest\ServiceManager
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        if (!isset($this->filesystem)) {
            $this->filesystem = new Filesystem();
        }
        return $this->filesystem;
    }

    /**
     * @param string $group
     *
     * @return string
     */
    public function getConfigFilename($group)
    {
        $conf_files = $this->config->get('conf_files');
        if (!isset($conf_files[$group])) {
            $filename = $this->config['conf_dir'] . '/' . $group . '.json';
            $conf_files[$group] = $filename;
        }
        $this->config->set('conf_files', $conf_files);
        return $conf_files[$group];
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasConfigFile($name)
    {
        $filename = $this->getConfigFilename($name);
        return is_file($filename) && is_readable($filename);
    }

    /**
     * @param string $group
     *
     * @return \Guzzle\Service\Builder\ServiceBuilder
     */
    public function load($group)
    {
        $confg = $this->hasConfigFile($group) ? $this->getConfigFilename($group) : array();
        return ServiceBuilder::factory($confg);
    }

    /**
     * @param string $group
     *
     * @return \Guzzle\Service\Builder\ServiceBuilder
     */
    public function offsetGet($group)
    {
        if (!isset($this[$group])) {

            // Load builder from file or instantiate an empty one.
            if ($this->hasConfigFile($group)) {
                $this[$group] = $this->load($group);
            } else {
                $this[$group] = ServiceBuilder::factory(array());
            }

            // Initialize the "removed" flag.
            $this->removed[$group] = array();
        }
        return parent::offsetGet($group);
    }

    /**
     * @param string $group
     * @param \Guzzle\Service\Builder\ServiceBuilder $builder
     */
    public function offsetSet($group, $builder)
    {
        if (!$builder instanceof ServiceBuilder) {
            throw new \UnexpectedValueException('Expecting value to be an instance of Guzzle\Service\Builder\ServiceBuilder');
        }
        parent::offsetSet($group, $builder);
    }

    /**
     * @param string $group
     */
    public function offsetUnset($group)
    {
        unset($this->removed[$group]);
        parent::offsetUnset($group);
    }

    /**
     * @param string $group
     *
     * @return \Guzzle\Service\Builder\ServiceBuilder
     */
    public function getBuilder($group)
    {
        return $this[$group];
    }

    /**
     * @param string $group
     * @param \Guzzle\Service\Builder\ServiceBuilder $builder
     *
     * @return \Acquia\Rest\ServiceManager
     */
    public function setBuilder($group, ServiceBuilder $builder)
    {
        $this[$group] = $builder;
        $this->removed[$group] = array();
        return $this;
    }

    /**
     * @param string $group
     * @param string $name
     * @param \Guzzle\Service\Client $client
     *
     * @return \Acquia\Rest\ServiceManager
     */
    public function setClient($group, $name, Client $client)
    {
        // Must also be service manager aware.
        if (!$client instanceof ServiceManagerAware) {
            throw new \UnexpectedValueException('Client must implement Acquia\Rest\ServiceManagerAware');
        }

        $builder = $this[$group];

        // Set the client in the service builder.
        $builder[$name] = $client;

        // This looks funky, but it is actually not overwriting the value we
        // just set. This snippet adds the builder config so that saving the
        // service will add this client as a service in the JSON file.
        // @see \Guzzle\Service\Builder\ServiceBuilder::set()
        $builder[$name] = array(
            'class' => get_class($client),
            'params' => $client->getBuilderParams(),
        );

        // If the client was previously removed, unset the remove flag.
        unset($this->removed[$group][$name]);

        return $this;
    }

    /**
     * @param string $group
     * @param string $name
     *
     * @return \Guzzle\Service\Client
     */
    public function getClient($group, $name)
    {
        return isset($this[$group][$name]) ? $this[$group][$name] : null;
    }

    /**
     * @param string $group
     * @param string $name
     *
     * @return \Acquia\Rest\ServiceManager
     */
    public function removeClient($group, $name)
    {
        unset($this[$group][$name]);
        $this->removed[$group][$name] = $name;
        return $this;
    }

    /**
     * Writes all service group configurations to the backend.
     *
     * @param boolean $overwrite
     */
    public function save($overwrite = false)
    {
        foreach ($this as $group => $builder) {
            $this->saveServiceGroup($group, $overwrite);
        }
    }

    /**
     * @param string $group
     * @param boolean $overwrite
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @see http://guzzlephp.org/webservice-client/using-the-service-builder.html#sourcing-from-a-json-document
     */
    public function saveServiceGroup($group, $overwrite = false)
    {
        $filename = $this->getConfigFilename($group);
        $hasConfigFile = $this->hasConfigFile($group);

        // This sucks, but it is the only way to get the builder config.
        // @todo Create a Guzzle issue to add a getBuilderConfig() method.
        $builder = $this[$group];
        $builderConfig = Json::decode($builder->serialize());

        // @todo Add validation.
        if (!$overwrite && $hasConfigFile) {
            $groupJson = file_get_contents($filename);
            $groupData = Json::decode($groupJson);
            $builderConfig = array_merge($groupData['services'], $builderConfig);
        }

        // Unset the services that are flagged to be removed then clear the
        // remove flag since action was taken.
        foreach ($this->removed[$group] as $name) {
            unset($builderConfig[$name]);
        }
        $this->removed[$group] = array();

        $json = Json::encode(array(
            'class' => get_class($builder),
            'services' => $builderConfig,
        ));

        $filesystem = $this->getFilesystem();

        if (!$hasConfigFile) {
            $filesystem->mkdir(dirname($filename), 0755);
        }

        $filesystem->dumpFile($filename, $json, 0600);
    }

    /**
     * @param string $group
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function deleteServiceGroup($group)
    {
        $filename = $this->getConfigFilename($group);
        $this->getFilesystem()->remove($filename);

        // Unlike normal arrays, unset() will throw errors here if the key
        // doesn't exist.
        if (isset($this[$group])) {
            unset($this[$group]);
        }

        unset($this->removed[$group]);
    }
}

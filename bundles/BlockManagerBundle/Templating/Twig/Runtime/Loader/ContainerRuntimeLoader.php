<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Runtime loader for Netgen Block Manager Twig runtimes.
 *
 * @deprecated Remove when support for Symfony 2.8 ends.
 */
class ContainerRuntimeLoader implements RuntimeLoaderInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $runtimeMap = array();

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addRuntime($class, $serviceId)
    {
        $this->runtimeMap[$class] = $serviceId;
    }

    /**
     * Creates the runtime implementation of a Twig element (filter/function/test).
     *
     * @param string $class A runtime class
     *
     * @return object|null The runtime instance or null if the loader does not know how to create the runtime for this class
     */
    public function load($class)
    {
        if (!array_key_exists($class, $this->runtimeMap)) {
            return null;
        }

        return $this->container->get($this->runtimeMap[$class]);
    }
}

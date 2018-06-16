<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Runtime loader for Netgen Layouts Twig runtimes.
 *
 * @deprecated Remove when support for Symfony 2.8 ends.
 */
final class ContainerRuntimeLoader implements RuntimeLoaderInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $runtimeMap = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addRuntime(string $class, string $serviceId): void
    {
        $this->runtimeMap[$class] = $serviceId;
    }

    public function load($class)
    {
        if (!array_key_exists($class, $this->runtimeMap)) {
            return null;
        }

        return $this->container->get($this->runtimeMap[$class]);
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait DefinitionClassCacheTrait
{
    /**
     * @var string[]
     */
    private $definitionClasses = [];

    private function getDefinitionClass(ContainerBuilder $container, string $serviceId): string
    {
        if (!isset($this->definitionClasses[$serviceId])) {
            $this->definitionClasses[$serviceId] = $container->getParameterBag()->resolveValue(
                $container->findDefinition($serviceId)->getClass()
            );
        }

        return $this->definitionClasses[$serviceId];
    }
}

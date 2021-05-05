<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait DefinitionClassTrait
{
    /**
     * @var string[]
     */
    private array $definitionClasses = [];

    private function getDefinitionClass(ContainerBuilder $container, string $serviceId): string
    {
        $this->definitionClasses[$serviceId] ??= $container->getParameterBag()->resolveValue(
            $container->findDefinition($serviceId)->getClass(),
        );

        return $this->definitionClasses[$serviceId];
    }
}

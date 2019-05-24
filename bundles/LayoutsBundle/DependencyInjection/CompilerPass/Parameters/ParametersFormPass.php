<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Parameters;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ParametersFormPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_layouts.parameters.form.parameters';
    private const TAG_NAME = 'netgen_layouts.parameter_type.form_mapper';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $parametersForm = $container->findDefinition(self::SERVICE_NAME);

        $mappers = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $formMapper => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['type'])) {
                    $mappers[$tag['type']] = new ServiceClosureArgument(new Reference($formMapper));

                    continue 2;
                }
            }

            $mapperClass = $this->getDefinitionClass($container, $formMapper);
            if (isset($mapperClass::$defaultParameterType)) {
                $mappers[$mapperClass::$defaultParameterType] = new ServiceClosureArgument(new Reference($formMapper));

                continue;
            }
        }

        $parametersForm->addArgument(new Definition(ServiceLocator::class, [$mappers]));
    }
}

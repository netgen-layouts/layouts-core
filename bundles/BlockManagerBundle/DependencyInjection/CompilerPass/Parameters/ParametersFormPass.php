<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ParametersFormPass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_block_manager.parameters.form.parameters';
    private const TAG_NAME = 'netgen_block_manager.parameters.form.mapper';

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
                    $mappers[$tag['type']] = new Reference($formMapper);
                    continue 2;
                }
            }

            $mapperClass = $this->getDefinitionClass($container, $formMapper);
            if (isset($mapperClass::$defaultParameterType)) {
                $mappers[$mapperClass::$defaultParameterType] = new Reference($formMapper);
                continue;
            }
        }

        $parametersForm->replaceArgument(0, $mappers);
    }
}

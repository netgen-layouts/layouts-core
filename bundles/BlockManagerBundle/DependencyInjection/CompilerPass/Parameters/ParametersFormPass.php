<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ParametersFormPass implements CompilerPassInterface
{
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
                if (!isset($tag['type'])) {
                    throw new RuntimeException(
                        "Parameter form mapper service definition must have a 'type' attribute in its' tag."
                    );
                }

                $mappers[$tag['type']] = new Reference($formMapper);
            }
        }

        $parametersForm->replaceArgument(0, $mappers);
    }
}

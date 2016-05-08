<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ValueLoaderRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ValueLoaderRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ValueLoaderRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\ValueLoaderRegistryPass::process
     */
    public function testProcess()
    {
        $valueLoaderRegistry = new Definition();
        $this->setDefinition('netgen_block_manager.collection.value_loader.registry', $valueLoaderRegistry);

        $valueLoader = new Definition();
        $valueLoader->addTag('netgen_block_manager.collection.value_loader');
        $this->setDefinition('netgen_block_manager.collection.value_loader.test', $valueLoader);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.value_loader.registry',
            'addValueLoader',
            array(
                new Reference('netgen_block_manager.collection.value_loader.test'),
            )
        );
    }
}

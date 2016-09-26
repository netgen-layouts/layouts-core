<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Configuration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\SourceRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SourceRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SourceRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\SourceRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\SourceRegistryPass::validateSources
     */
    public function testProcess()
    {
        $this->setParameter('netgen_block_manager.sources', array());
        $this->setParameter('netgen_block_manager.query_types', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.source', new Definition());

        $source = new Definition();
        $source->addTag('netgen_block_manager.configuration.source');
        $this->setDefinition('netgen_block_manager.configuration.source.test', $source);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.configuration.registry.source',
            'addSource',
            array(
                new Reference('netgen_block_manager.configuration.source.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\SourceRegistryPass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration\SourceRegistryPass::validateSources
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWithNoQueryType()
    {
        $this->setParameter(
            'netgen_block_manager.sources',
            array(
                'test' => array(
                    'queries' => array(
                        'query' => array(
                            'query_type' => 'type',
                        ),
                    ),
                ),
            )
        );

        $this->setParameter('netgen_block_manager.query_types', array());

        $this->setDefinition('netgen_block_manager.configuration.registry.source', new Definition());

        $source = new Definition();
        $source->addTag('netgen_block_manager.configuration.source');
        $this->setDefinition('netgen_block_manager.configuration.source.test', $source);

        $this->compile();
    }
}

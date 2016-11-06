<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypeRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class QueryTypeRegistryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new QueryTypeRegistryPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypeRegistryPass::process
     */
    public function testProcess()
    {
        $queryTypes = array('query_type' => array('config'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);
        $this->setParameter('netgen_block_manager.collection.query_type.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.collection.query_type.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.collection.query_type.class', 'definition_class');
        $this->setParameter('netgen_block_manager.collection.query_type.factory.class', 'factory_class');

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', array('type' => 'query_type'));
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.query_type.configuration.query_type',
            'config_class'
        );

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.query_type.query_type',
            'definition_class'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.collection.query_type.query_type',
            0,
            'query_type'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.collection.query_type.query_type',
            1,
            new Reference('netgen_block_manager.collection.query_type.handler.test')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.collection.query_type.query_type',
            2,
            new Reference('netgen_block_manager.collection.query_type.configuration.query_type')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.query_type',
            'addQueryType',
            array(
                new Reference('netgen_block_manager.collection.query_type.query_type'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypeRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoTagType()
    {
        $queryTypes = array('query_type' => array('config'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);
        $this->setParameter('netgen_block_manager.collection.query_type.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.collection.query_type.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.collection.query_type.class', 'definition_class');

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler');
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypeRegistryPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsExceptionWithNoHandler()
    {
        $queryTypes = array('query_type' => array('config'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);
        $this->setParameter('netgen_block_manager.collection.query_type.configuration.factory.class', 'factory_class');
        $this->setParameter('netgen_block_manager.collection.query_type.configuration.class', 'config_class');
        $this->setParameter('netgen_block_manager.collection.query_type.class', 'definition_class');

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', array('type' => 'other'));
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }
}

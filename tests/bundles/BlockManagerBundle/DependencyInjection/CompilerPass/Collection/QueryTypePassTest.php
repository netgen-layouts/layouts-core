<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class QueryTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcess()
    {
        $queryTypes = array('query_type' => array('config'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', array('type' => 'query_type'));
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.query_type.query_type'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.query_type',
            'addQueryType',
            array(
                'query_type',
                new Reference('netgen_block_manager.collection.query_type.query_type'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessWithCustomHandler()
    {
        $queryTypes = array('query_type' => array('handler' => 'custom'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', array('type' => 'custom'));
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.query_type.query_type'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.query_type',
            'addQueryType',
            array(
                'query_type',
                new Reference('netgen_block_manager.collection.query_type.query_type'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Query type handler definition must have a 'type' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagType()
    {
        $queryTypes = array('query_type' => array('config'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler');
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Query type handler for "query_type" query type does not exist.
     */
    public function testProcessThrowsExceptionWithNoHandler()
    {
        $queryTypes = array('query_type' => array('config'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', array('type' => 'other'));
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Query type handler for "query_type" query type does not exist.
     */
    public function testProcessThrowsExceptionWithNoCustomHandler()
    {
        $queryTypes = array('query_type' => array('handler' => 'custom'));
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', array('type' => 'other'));
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     * @doesNotPerformAssertions
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new QueryTypePass());
    }
}

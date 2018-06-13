<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class QueryTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcess()
    {
        $queryTypes = ['query_type' => ['config']];
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', ['type' => 'query_type']);
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.query_type.query_type'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.query_type',
            'addQueryType',
            [
                'query_type',
                new Reference('netgen_block_manager.collection.query_type.query_type'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessWithCustomHandler()
    {
        $queryTypes = ['query_type' => ['handler' => 'custom']];
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', ['type' => 'custom']);
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_block_manager.collection.query_type.query_type'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.registry.query_type',
            'addQueryType',
            [
                'query_type',
                new Reference('netgen_block_manager.collection.query_type.query_type'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Query type handler definition must have a 'type' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagType()
    {
        $queryTypes = ['query_type' => ['config']];
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
        $queryTypes = ['query_type' => ['config']];
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', ['type' => 'other']);
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
        $queryTypes = ['query_type' => ['handler' => 'custom']];
        $this->setParameter('netgen_block_manager.query_types', $queryTypes);

        $this->setDefinition('netgen_block_manager.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition();
        $queryTypeHandler->addTag('netgen_block_manager.collection.query_type_handler', ['type' => 'other']);
        $this->setDefinition('netgen_block_manager.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
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

<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineTargetHandlerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineTargetHandlerPass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     */
    public function testProcess()
    {
        $layoutResolverHandler = new Definition();
        $layoutResolverHandler->addArgument(array());
        $layoutResolverHandler->addArgument(array());
        $layoutResolverHandler->addArgument(array());

        $this->setDefinition('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler', $layoutResolverHandler);

        $targetHandler = new Definition();
        $targetHandler->addTag(
            'netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler',
            array(
                'target_type' => 'test',
            )
        );
        $this->setDefinition('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.test', $targetHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.persistence.doctrine.layout_resolver.query_handler',
            2,
            array(
                'test' => new Reference('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.test'),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testProcessThrowsRuntimeExceptionWhenNoIdentifier()
    {
        $this->setDefinition('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler', new Definition());

        $targetHandler = new Definition();
        $targetHandler->addTag('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler');
        $this->setDefinition('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler.target_handler.test', $targetHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\LayoutResolver;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class DoctrineTargetHandlerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     */
    public function testProcess()
    {
        $layoutResolverHandler = new Definition();
        $layoutResolverHandler->addArgument([]);
        $layoutResolverHandler->addArgument([]);
        $layoutResolverHandler->addArgument([]);

        $this->setDefinition('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler', $layoutResolverHandler);

        $targetHandler = new Definition();
        $targetHandler->addTag(
            'netgen_block_manager.layout.resolver.target_handler.doctrine',
            [
                'target_type' => 'test',
            ]
        );
        $this->setDefinition('netgen_block_manager.layout.resolver.target_handler.doctrine.test', $targetHandler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.persistence.doctrine.layout_resolver.query_handler',
            2,
            [
                'test' => new Reference('netgen_block_manager.layout.resolver.target_handler.doctrine.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Doctrine target handler service tags should have an "target_type" attribute.
     */
    public function testProcessThrowsRuntimeExceptionWhenNoIdentifier()
    {
        $this->setDefinition('netgen_block_manager.persistence.doctrine.layout_resolver.query_handler', new Definition());

        $targetHandler = new Definition();
        $targetHandler->addTag('netgen_block_manager.layout.resolver.target_handler.doctrine');
        $this->setDefinition('netgen_block_manager.layout.resolver.target_handler.doctrine.test', $targetHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\LayoutResolver\DoctrineTargetHandlerPass::process
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
        $container->addCompilerPass(new DoctrineTargetHandlerPass());
    }
}

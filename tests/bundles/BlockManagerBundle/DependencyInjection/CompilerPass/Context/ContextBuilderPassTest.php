<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Context;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Context\ContextBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ContextBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Context\ContextBuilderPass::process
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.context.builder', new Definition());

        $contextProvider = new Definition();
        $contextProvider->addTag('netgen_block_manager.context.provider');
        $this->setDefinition('netgen_block_manager.context.provider.test', $contextProvider);

        $contextProvider2 = new Definition();
        $contextProvider2->addTag('netgen_block_manager.context.provider');
        $this->setDefinition('netgen_block_manager.context.provider.test2', $contextProvider2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.context.builder',
            'registerProvider',
            [new Reference('netgen_block_manager.context.provider.test')],
            0
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.context.builder',
            'registerProvider',
            [new Reference('netgen_block_manager.context.provider.test2')],
            1
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Context\ContextBuilderPass::process
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
        $container->addCompilerPass(new ContextBuilderPass());
    }
}

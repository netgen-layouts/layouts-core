<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\VisibilityResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class VisibilityResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\VisibilityResolverPass::process
     */
    public function testProcess()
    {
        $visibilityResolver = new Definition();
        $visibilityResolver->addArgument(array());
        $this->setDefinition('netgen_block_manager.collection.item.visibility_resolver', $visibilityResolver);

        $voter = new Definition();
        $voter->addTag('netgen_block_manager.collection.item.visibility_resolver.voter');
        $this->setDefinition('netgen_block_manager.collection.item.visibility_resolver.voter.test', $voter);

        $voter2 = new Definition();
        $voter2->addTag('netgen_block_manager.collection.item.visibility_resolver.voter');
        $this->setDefinition('netgen_block_manager.collection.item.visibility_resolver.voter.test2', $voter2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.item.visibility_resolver',
            'setVoters',
            array(
                array(
                    new Reference('netgen_block_manager.collection.item.visibility_resolver.voter.test'),
                    new Reference('netgen_block_manager.collection.item.visibility_resolver.voter.test2'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\VisibilityResolverPass::process
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
        $container->addCompilerPass(new VisibilityResolverPass());
    }
}

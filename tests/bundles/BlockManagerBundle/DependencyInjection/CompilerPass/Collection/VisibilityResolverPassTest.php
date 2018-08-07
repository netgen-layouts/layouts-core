<?php

declare(strict_types=1);

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
    public function testProcess(): void
    {
        $cacheableResolver = new Definition();
        $cacheableResolver->addArgument([]);
        $this->setDefinition('netgen_block_manager.collection.item_visibility_resolver', $cacheableResolver);

        $voter = new Definition();
        $voter->addTag('netgen_block_manager.collection.item_visibility_resolver.voter');
        $this->setDefinition('netgen_block_manager.collection.item_visibility_resolver.voter.test', $voter);

        $voter2 = new Definition();
        $voter2->addTag('netgen_block_manager.collection.item_visibility_resolver.voter');
        $this->setDefinition('netgen_block_manager.collection.item_visibility_resolver.voter.test2', $voter2);

        $this->compile();

        self::assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.collection.item_visibility_resolver',
            'setVoters',
            [
                [
                    new Reference('netgen_block_manager.collection.item_visibility_resolver.voter.test'),
                    new Reference('netgen_block_manager.collection.item_visibility_resolver.voter.test2'),
                ],
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection\VisibilityResolverPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new VisibilityResolverPass());
    }
}

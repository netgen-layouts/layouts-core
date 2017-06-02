<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\HttpCache\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\Block\CacheableResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CacheableResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\Block\CacheableResolverPass::process
     */
    public function testProcess()
    {
        $cacheableResolver = new Definition();
        $cacheableResolver->addArgument(array());
        $this->setDefinition('netgen_block_manager.http_cache.block.cacheable_resolver', $cacheableResolver);

        $voter = new Definition();
        $voter->addTag('netgen_block_manager.http_cache.block.cacheable_resolver.voter');
        $this->setDefinition('netgen_block_manager.http_cache.block.cacheable_resolver.voter.test', $voter);

        $voter2 = new Definition();
        $voter2->addTag('netgen_block_manager.http_cache.block.cacheable_resolver.voter');
        $this->setDefinition('netgen_block_manager.http_cache.block.cacheable_resolver.voter.test2', $voter2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.http_cache.block.cacheable_resolver',
            'setVoters',
            array(
                array(
                    new Reference('netgen_block_manager.http_cache.block.cacheable_resolver.voter.test'),
                    new Reference('netgen_block_manager.http_cache.block.cacheable_resolver.voter.test2'),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\Block\CacheableResolverPass::process
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
        $container->addCompilerPass(new CacheableResolverPass());
    }
}

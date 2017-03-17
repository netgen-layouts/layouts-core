<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\HttpCache;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigureHttpCachePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::configureBlockCache
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::configureLayoutCache
     */
    public function testProcess()
    {
        $this->setDefinition('netgen_block_manager.http_cache.client', new Definition());

        $this->setParameter(
            'netgen_block_manager.http_cache',
            array(
                'invalidation' => array(
                    'enabled' => true,
                    'default_strategy' => 'ban',
                    'strategies' => array(
                        'ban' => array(
                            'block' => array(
                                'invalidator' => 'ban.block.invalidator',
                                'tagger' => 'ban.block.tagger',
                            ),
                            'layout' => array(
                                'invalidator' => 'ban.layout.invalidator',
                                'tagger' => 'ban.layout.tagger',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.block.tagger',
            'ban.block.tagger'
        );

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.block.invalidator',
            'ban.block.invalidator'
        );

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.layout.tagger',
            'ban.layout.tagger'
        );

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.layout.invalidator',
            'ban.layout.invalidator'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Invalidation strategy "unknown" does not exist in Netgen Block Manager configuration.
     */
    public function testProcessWithoutDefinedStrategy()
    {
        $this->setDefinition('netgen_block_manager.http_cache.client', new Definition());

        $this->setParameter(
            'netgen_block_manager.http_cache',
            array(
                'invalidation' => array(
                    'enabled' => true,
                    'default_strategy' => 'unknown',
                    'strategies' => array(
                        'ban' => array(
                            'block' => array(
                                'invalidator' => 'ban.block.invalidator',
                                'tagger' => 'ban.block.tagger',
                            ),
                            'layout' => array(
                                'invalidator' => 'ban.layout.invalidator',
                                'tagger' => 'ban.layout.tagger',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     */
    public function testProcessWithDisabledInvalidation()
    {
        $this->setDefinition('netgen_block_manager.http_cache.client', new Definition());

        $this->setParameter(
            'netgen_block_manager.http_cache',
            array(
                'invalidation' => array(
                    'enabled' => false,
                    'default_strategy' => 'ban',
                    'strategies' => array(
                        'ban' => array(
                            'block' => array(
                                'invalidator' => 'ban.block.invalidator',
                                'tagger' => 'ban.block.tagger',
                            ),
                            'layout' => array(
                                'invalidator' => 'ban.layout.invalidator',
                                'tagger' => 'ban.layout.tagger',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'netgen_block_manager.http_cache.client',
            'netgen_block_manager.http_cache.client.null'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\HttpCache\ConfigureHttpCachePass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertEmpty($this->container->getAliases());
        // The container has at least self ("service_container") as the service
        $this->assertCount(1, $this->container->getServiceIds());
        $this->assertEmpty($this->container->getParameterBag()->all());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigureHttpCachePass());
    }
}

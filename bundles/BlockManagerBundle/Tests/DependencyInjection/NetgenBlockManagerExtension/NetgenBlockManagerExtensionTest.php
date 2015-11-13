<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class NetgenBlockManagerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * Return an array of container extensions that need to be registered for
     * each test (usually just the container extension you are testing).
     *
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return array(
            new NetgenBlockManagerExtension()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     */
    public function testDefaultSettings()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('netgen_block_manager.blocks', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.layout_view', array());
        $this->assertContainerBuilderHasParameter('netgen_block_manager.block_groups', array());
        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.pagelayout',
            'NetgenBlockManagerBundle::pagelayout_empty.html.twig'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     */
    public function testServices()
    {
    }
}

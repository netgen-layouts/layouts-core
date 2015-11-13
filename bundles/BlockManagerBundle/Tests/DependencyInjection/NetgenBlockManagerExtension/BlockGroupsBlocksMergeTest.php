<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class BlockGroupsBlocksMergeTest extends AbstractExtensionTestCase
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
            new NetgenBlockManagerExtension(),
        );
    }

    /**
     * Returns minimal working configuration for the extension.
     */
    protected function getMinimalConfiguration()
    {
        return array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'paragraph'),
                ),
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlockGroupsNodeDefinition
     */
    public function testBlocksMerge()
    {
        $config = array(
            'block_groups' => array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('image'),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.block_groups',
            array(
                'block_group' => array(
                    'name' => 'block_group',
                    'blocks' => array('title', 'paragraph', 'image'),
                ),
            )
        );
    }
}

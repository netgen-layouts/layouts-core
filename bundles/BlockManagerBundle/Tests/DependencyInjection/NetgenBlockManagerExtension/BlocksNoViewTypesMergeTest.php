<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class BlocksNoViewTypesMergeTest extends AbstractExtensionTestCase
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
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default'
                        ),
                        'large' => array(
                            'name' => 'Large'
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getBlocksNodeDefinition
     */
    public function testNoViewTypesMerge()
    {
        $config = array(
            'blocks' => array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title'
                        ),
                        'image' => array(
                            'name' => 'Image'
                        ),
                    ),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.blocks',
            array(
                'block' => array(
                    'name' => 'block',
                    'view_types' => array(
                        'title' => array(
                            'name' => 'Title'
                        ),
                        'image' => array(
                            'name' => 'Image'
                        ),
                    ),
                ),
            )
        );
    }
}

<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class LayoutsNoZonesMergeTest extends AbstractExtensionTestCase
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
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'left' => array(
                            'name' => 'Left',
                        ),
                        'right' => array(
                            'name' => 'Right',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testNoViewTypesMerge()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'top' => array(
                            'name' => 'Top',
                        ),
                        'bottom' => array(
                            'name' => 'Bottom',
                        ),
                    ),
                ),
            ),
        );

        $this->load($config);

        $this->assertContainerBuilderHasParameter(
            'netgen_block_manager.layouts',
            array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'top' => array(
                            'name' => 'Top',
                            'allowed_blocks' => array(),
                        ),
                        'bottom' => array(
                            'name' => 'Bottom',
                            'allowed_blocks' => array(),
                        ),
                    ),
                ),
            )
        );
    }
}

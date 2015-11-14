<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\NetgenBlockManagerExtension;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class LayoutsTest extends AbstractExtensionTestCase
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getAvailableNodeDefinitions
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testDefaultLayoutSettings()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
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
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array(),
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testDefaultLayoutSettingsWithAllowedBlocks()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array('title', 'paragraph'),
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
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     */
    public function testDefaultLayoutSettingsWithNonUniqueAllowedBlocks()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array('title', 'paragraph', 'title'),
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
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array('title', 'paragraph'),
                        ),
                    ),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithEmptyLayouts()
    {
        $config = array('layouts' => array());
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithInvalidLayouts()
    {
        $config = array('layouts' => 'layouts');
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithNoName()
    {
        $config = array('layouts' => array('layout' => array()));
        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithEmptyName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => '',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithInvalidName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithNoZones()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithEmptyZones()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithInvalidZones()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => 'zone',
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithNoZoneName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithEmptyZoneName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => '',
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithInvalidZoneName()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => array(),
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithEmptyAllowedBlocks()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => 'zone',
                        'allowed_blocks' => array(),
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithInvalidAllowedBlocks()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => 'zone',
                        'allowed_blocks' => 'allowed_blocks',
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithEmptyAllowedBlockItem()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array(''),
                        ),
                    ),
                ),
            ),
        );

        $this->load($config);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getLayoutsNodeDefinition
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDefaultLayoutSettingsWithInvalidAllowedBlockItem()
    {
        $config = array(
            'layouts' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_blocks' => array(array()),
                        ),
                    ),
                ),
            ),
        );

        $this->load($config);
    }
}

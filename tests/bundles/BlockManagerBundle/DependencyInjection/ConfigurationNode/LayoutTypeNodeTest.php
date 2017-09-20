<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class LayoutTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeSettings()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'name' => 'layout',
                        'icon' => '/icon.svg',
                        'zones' => array(
                            'zone' => array(
                                'name' => 'zone',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'zones' => array(
                        'zone' => array(
                            'name' => 'zone',
                            'allowed_block_definitions' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeSettingsWithNoIcon()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'icon' => null,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeSettingsWithNullIcon()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'icon' => null,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'icon' => null,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeSettingsNoZonesMerge()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
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
            ),
            array(
                'layout_types' => array(
                    'layout' => array(
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
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'zones' => array(
                        'top' => array(
                            'name' => 'Top',
                            'allowed_block_definitions' => array(),
                        ),
                        'bottom' => array(
                            'name' => 'Bottom',
                            'allowed_block_definitions' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeSettingsWithAllowedBlockDefinitions()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'zones' => array(
                            'zone' => array(
                                'allowed_block_definitions' => array('title', 'text'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'zones' => array(
                        'zone' => array(
                            'allowed_block_definitions' => array('title', 'text'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones.*.allowed_block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testLayoutTypeSettingsWithNonUniqueAllowedBlockDefinitions()
    {
        $config = array(
            array(
                'layout_types' => array(
                    'layout' => array(
                        'zones' => array(
                            'zone' => array(
                                'allowed_block_definitions' => array('title', 'text', 'title'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'layout_types' => array(
                'layout' => array(
                    'zones' => array(
                        'zone' => array(
                            'allowed_block_definitions' => array('title', 'text'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones.*.allowed_block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithEmptyLayouts()
    {
        $config = array('layout_types' => array());
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoName()
    {
        $config = array('layout_types' => array('layout' => array()));
        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoZones()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeWithEmptyIcon()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'Layout',
                    'icon' => '',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config), 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeWithNonStringIcon()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'Layout',
                    'icon' => 42,
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config), 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithEmptyZones()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoZoneName()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithEmptyAllowedBlockDefinitions()
    {
        $config = array(
            'layout_types' => array(
                'layout' => array(
                    'name' => 'layout',
                    'zones' => array(
                        'name' => 'zone',
                        'allowed_block_definitions' => array(),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case.
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    private function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}

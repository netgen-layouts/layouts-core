<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class BlockTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettings()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'name' => 'Block type',
                        'icon' => '/icon.svg',
                        'definition_identifier' => 'title',
                        'defaults' => array(
                            'name' => 'Name',
                            'view_type' => 'large',
                            'item_view_type' => 'standard',
                            'parameters' => array(
                                'param1' => 'value1',
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'name' => 'Block type',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'definition_identifier' => 'title',
                    'defaults' => array(
                        'name' => 'Name',
                        'view_type' => 'large',
                        'item_view_type' => 'standard',
                        'parameters' => array(
                            'param1' => 'value1',
                            'param2' => 'value2',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoIcon()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block' => array(
                    'icon' => null,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNullIcon()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block' => array(
                        'icon' => null,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block' => array(
                    'icon' => null,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoName()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.name'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoNameAndWithDefinitionIdentifierWhenDisabled()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'enabled' => false,
                        'icon' => '/icon.svg',
                        'definition_identifier' => 'title',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'enabled' => false,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
                    'defaults' => array(
                        'name' => '',
                        'view_type' => '',
                        'item_view_type' => '',
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefinitionIdentifier()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.definition_identifier'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultName()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'name' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.name'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultViewType()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'view_type' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.view_type'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultItemViewType()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'item_view_type' => '',
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.item_view_type'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultParameters()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoParametersMerge()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(
                            'parameters' => array(
                                'param1' => 'value1',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_types' => array(
                    'block_type' => array(
                        'defaults' => array(
                            'parameters' => array(
                                'param2' => 'value2',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'parameters' => array(
                            'param2' => 'value2',
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaults()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'defaults' => array(
                        'name' => '',
                        'view_type' => '',
                        'item_view_type' => '',
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeWithEmptyIcon()
    {
        $config = array(
            'block_types' => array(
                'block' => array(
                    'name' => 'Block',
                    'icon' => '',
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config), 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeWithNonStringIcon()
    {
        $config = array(
            'block_types' => array(
                'block' => array(
                    'name' => 'Block',
                    'icon' => 42,
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config), 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoBlockTypes()
    {
        $config = array(
            'block_types' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoNameAndWithDefinitionIdentifier()
    {
        $config = array(
            'block_types' => array(
                'block_type' => array(
                    'definition_identifier' => 'title',
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
    protected function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}

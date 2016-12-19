<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

class BlockTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockTypeSettings()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'name' => 'Block type',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockTypeSettingsWithNoNameAndWithDefinitionIdentifierWhenDisabled()
    {
        $config = array(
            array(
                'block_types' => array(
                    'block_type' => array(
                        'enabled' => false,
                        'definition_identifier' => 'title',
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_types' => array(
                'block_type' => array(
                    'enabled' => false,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
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

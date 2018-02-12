<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\BlockManager\Block\Form\FullEditType;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettings()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'name' => 'Block',
                        'icon' => '/icon.svg',
                        'handler' => 'handler',
                        'translatable' => true,
                        'collections' => array(
                            'default' => array(
                                'valid_item_types' => array('type3', 'type4'),
                                'valid_query_types' => array('type1', 'type2'),
                            ),
                        ),
                        'forms' => array(
                            'full' => array(
                                'type' => 'test_form',
                                'enabled' => true,
                            ),
                            'design' => array(
                                'type' => 'design_type',
                                'enabled' => false,
                            ),
                            'content' => array(
                                'type' => 'content_type',
                                'enabled' => false,
                            ),
                        ),
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                    'other' => array(
                                        'name' => 'Other',
                                        'enabled' => false,
                                    ),
                                ),
                                'valid_parameters' => array('param1', 'param2'),
                            ),
                            'large' => array(
                                'name' => 'Large',
                                'enabled' => false,
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                                'valid_parameters' => null,
                            ),
                            'medium' => array(
                                'name' => 'Medium',
                                'enabled' => true,
                                'item_view_types' => array(
                                    'standard_with_intro' => array(
                                        'name' => 'Standard (with intro)',
                                    ),
                                ),
                            ),
                            'small' => array(
                                'name' => 'Small',
                                'enabled' => true,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'icon' => '/icon.svg',
                    'handler' => 'handler',
                    'translatable' => true,
                    'enabled' => true,
                    'collections' => array(
                        'default' => array(
                            'valid_item_types' => array('type3', 'type4'),
                            'valid_query_types' => array('type1', 'type2'),
                        ),
                    ),
                    'forms' => array(
                        'full' => array(
                            'type' => 'test_form',
                            'enabled' => true,
                        ),
                        'design' => array(
                            'type' => 'design_type',
                            'enabled' => false,
                        ),
                        'content' => array(
                            'type' => 'content_type',
                            'enabled' => false,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ),
                                'other' => array(
                                    'name' => 'Disabled',
                                    'enabled' => false,
                                ),
                            ),
                            'valid_parameters' => array('param1', 'param2'),
                        ),
                        'large' => array(
                            'name' => 'Disabled',
                            'enabled' => false,
                            'item_view_types' => array(),
                            'valid_parameters' => null,
                        ),
                        'medium' => array(
                            'name' => 'Medium',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard_with_intro' => array(
                                    'name' => 'Standard (with intro)',
                                    'enabled' => true,
                                ),
                            ),
                            'valid_parameters' => null,
                        ),
                        'small' => array(
                            'name' => 'Small',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ),
                            ),
                            'valid_parameters' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithNoIcon()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'icon' => null,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithNullIcon()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'icon' => null,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'icon' => null,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithNoHandler()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.handler'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithDisabledBlockDefinition()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'enabled' => false,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'enabled' => false,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.enabled'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithNoTranslatableConfig()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'translatable' => false,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.translatable'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithEnabledTranslatableConfig()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'translatable' => true,
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'translatable' => true,
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.translatable'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithNoCollections()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithDefaultCollectionConfig()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_item_types' => null,
                            'valid_query_types' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithValidQueryTypes()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(
                                'valid_query_types' => array('type1', 'type2'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_query_types' => array('type1', 'type2'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithAllValidQueryTypes()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(
                                'valid_query_types' => null,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_query_types' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithEmptyValidQueryTypes()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(
                                'valid_query_types' => array(),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_query_types' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithValidItemTypes()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(
                                'valid_item_types' => array('type1', 'type2'),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_item_types' => array('type1', 'type2'),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_item_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithAllValidItemTypes()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(
                                'valid_item_types' => null,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_item_types' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_item_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithEmptyValidItemTypes()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'collections' => array(
                            'default' => array(
                                'valid_item_types' => array(),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'collections' => array(
                        'default' => array(
                            'valid_item_types' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_item_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithDesignAndContentForms()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'forms' => array(
                            'full' => array(
                                'enabled' => false,
                            ),
                            'design' => array(
                                'type' => 'design_form',
                                'enabled' => true,
                            ),
                            'content' => array(
                                'type' => 'content_form',
                                'enabled' => true,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'forms' => array(
                        'full' => array(
                            'type' => FullEditType::class,
                            'enabled' => false,
                        ),
                        'design' => array(
                            'type' => 'design_form',
                            'enabled' => true,
                        ),
                        'content' => array(
                            'type' => 'content_form',
                            'enabled' => true,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.forms'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsViewTypesMerge()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                                'valid_parameters' => array('param1', 'param2'),
                            ),
                            'large' => array(
                                'name' => 'Large',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                                'valid_parameters' => array('param3', 'param4'),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'title' => array(
                                'name' => 'Title',
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                                'valid_parameters' => array('param5', 'param6'),
                            ),
                            'large' => array(
                                'enabled' => false,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ),
                            ),
                            'valid_parameters' => array('param1', 'param2'),
                        ),
                        'title' => array(
                            'name' => 'Title',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ),
                            ),
                            'valid_parameters' => array('param5', 'param6'),
                        ),
                        'large' => array(
                            'name' => 'Disabled',
                            'enabled' => false,
                            'item_view_types' => array(),
                            'valid_parameters' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.view_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsItemViewTypesMerge()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'enabled' => true,
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'enabled' => true,
                                'item_view_types' => array(
                                    'other' => array(
                                        'name' => 'Other',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ),
                                'other' => array(
                                    'name' => 'Other',
                                    'enabled' => true,
                                ),
                            ),
                            'valid_parameters' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.view_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     */
    public function testBlockDefinitionSettingsWithoutValidParameters()
    {
        $config = array(
            array(
                'block_definitions' => array(
                    'block' => array(
                        'view_types' => array(
                            'default' => array(
                                'name' => 'Default',
                                'enabled' => true,
                                'item_view_types' => array(
                                    'standard' => array(
                                        'name' => 'Standard',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $expectedConfig = array(
            'block_definitions' => array(
                'block' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ),
                            ),
                            'valid_parameters' => null,
                        ),
                    ),
                ),
            ),
        );

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.view_types.*'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoName()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEmptyIcon()
    {
        $config = array(
            'block_definitions' => array(
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNonStringIcon()
    {
        $config = array(
            'block_definitions' => array(
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoBlockDefinitions()
    {
        $config = array(
            'block_definitions' => array(),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEmptyCollection()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'collections' => array(
                        'default' => array(
                            'valid_item_types' => array(),
                            'valid_query_types' => array(),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config), 'Collections need to allow at least one item type or at least one query type.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithMissingContentForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'forms' => array(
                        'full' => array(
                            'enabled' => false,
                        ),
                        'design' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithMissingDesignForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'forms' => array(
                        'full' => array(
                            'enabled' => false,
                        ),
                        'content' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithFullAndDesignForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'forms' => array(
                        'full' => array(
                            'enabled' => true,
                        ),
                        'design' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithFullAndContentForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'forms' => array(
                        'full' => array(
                            'enabled' => true,
                        ),
                        'content' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithFullAndDesignAndContentForm()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'forms' => array(
                        'full' => array(
                            'enabled' => true,
                        ),
                        'design' => array(
                            'enabled' => true,
                        ),
                        'content' => array(
                            'enabled' => true,
                        ),
                    ),
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(
                                'standard' => array(
                                    'name' => 'Standard',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoViewTypes()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'view_types' => array(),
                ),
            ),
        );

        $this->assertConfigurationIsInvalid(array($config));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoItemViewTypes()
    {
        $config = array(
            'block_definitions' => array(
                'block' => array(
                    'name' => 'Block',
                    'view_types' => array(
                        'default' => array(
                            'name' => 'Default',
                            'item_view_types' => array(),
                        ),
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
    protected function getConfiguration()
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}

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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettings()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'name' => 'Block',
                        'icon' => '/icon.svg',
                        'handler' => 'handler',
                        'translatable' => true,
                        'collections' => [
                            'default' => [
                                'valid_item_types' => ['type3', 'type4'],
                                'valid_query_types' => ['type1', 'type2'],
                            ],
                        ],
                        'forms' => [
                            'full' => [
                                'type' => 'test_form',
                                'enabled' => true,
                            ],
                            'design' => [
                                'type' => 'design_type',
                                'enabled' => false,
                            ],
                            'content' => [
                                'type' => 'content_type',
                                'enabled' => false,
                            ],
                        ],
                        'view_types' => [
                            'default' => [
                                'name' => 'Default',
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                    'other' => [
                                        'name' => 'Other',
                                        'enabled' => false,
                                    ],
                                ],
                                'valid_parameters' => ['param1', 'param2'],
                            ],
                            'large' => [
                                'name' => 'Large',
                                'enabled' => false,
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                ],
                                'valid_parameters' => null,
                            ],
                            'medium' => [
                                'name' => 'Medium',
                                'enabled' => true,
                                'item_view_types' => [
                                    'standard_with_intro' => [
                                        'name' => 'Standard (with intro)',
                                    ],
                                ],
                            ],
                            'small' => [
                                'name' => 'Small',
                                'enabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'icon' => '/icon.svg',
                    'handler' => 'handler',
                    'translatable' => true,
                    'enabled' => true,
                    'collections' => [
                        'default' => [
                            'valid_item_types' => ['type3', 'type4'],
                            'valid_query_types' => ['type1', 'type2'],
                        ],
                    ],
                    'forms' => [
                        'full' => [
                            'type' => 'test_form',
                            'enabled' => true,
                        ],
                        'design' => [
                            'type' => 'design_type',
                            'enabled' => false,
                        ],
                        'content' => [
                            'type' => 'content_type',
                            'enabled' => false,
                        ],
                    ],
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ],
                                'other' => [
                                    'name' => 'Disabled',
                                    'enabled' => false,
                                ],
                            ],
                            'valid_parameters' => ['param1', 'param2'],
                        ],
                        'large' => [
                            'name' => 'Disabled',
                            'enabled' => false,
                            'item_view_types' => [],
                            'valid_parameters' => null,
                        ],
                        'medium' => [
                            'name' => 'Medium',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard_with_intro' => [
                                    'name' => 'Standard (with intro)',
                                    'enabled' => true,
                                ],
                            ],
                            'valid_parameters' => null,
                        ],
                        'small' => [
                            'name' => 'Small',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ],
                            ],
                            'valid_parameters' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoIcon()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNullIcon()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'icon' => null,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoHandler()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.handler'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithDisabledBlockDefinition()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'enabled' => false,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'enabled' => false,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.enabled'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoTranslatableConfig()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'translatable' => false,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.translatable'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEnabledTranslatableConfig()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'translatable' => true,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'translatable' => true,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.translatable'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoCollections()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithDefaultCollectionConfig()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_item_types' => null,
                            'valid_query_types' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithValidQueryTypes()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [
                                'valid_query_types' => ['type1', 'type2'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_query_types' => ['type1', 'type2'],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithAllValidQueryTypes()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [
                                'valid_query_types' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_query_types' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEmptyValidQueryTypes()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [
                                'valid_query_types' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_query_types' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithValidItemTypes()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [
                                'valid_item_types' => ['type1', 'type2'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_item_types' => ['type1', 'type2'],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_item_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithAllValidItemTypes()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [
                                'valid_item_types' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_item_types' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_item_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEmptyValidItemTypes()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'collections' => [
                            'default' => [
                                'valid_item_types' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'collections' => [
                        'default' => [
                            'valid_item_types' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.collections.default.valid_item_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithDesignAndContentForms()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'forms' => [
                            'full' => [
                                'enabled' => false,
                            ],
                            'design' => [
                                'type' => 'design_form',
                                'enabled' => true,
                            ],
                            'content' => [
                                'type' => 'content_form',
                                'enabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'forms' => [
                        'full' => [
                            'type' => FullEditType::class,
                            'enabled' => false,
                        ],
                        'design' => [
                            'type' => 'design_form',
                            'enabled' => true,
                        ],
                        'content' => [
                            'type' => 'content_form',
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.forms'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsViewTypesMerge()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'view_types' => [
                            'default' => [
                                'name' => 'Default',
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                ],
                                'valid_parameters' => ['param1', 'param2'],
                            ],
                            'large' => [
                                'name' => 'Large',
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                ],
                                'valid_parameters' => ['param3', 'param4'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'block_definitions' => [
                    'block' => [
                        'view_types' => [
                            'title' => [
                                'name' => 'Title',
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                ],
                                'valid_parameters' => ['param5', 'param6'],
                            ],
                            'large' => [
                                'enabled' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ],
                            ],
                            'valid_parameters' => ['param1', 'param2'],
                        ],
                        'title' => [
                            'name' => 'Title',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ],
                            ],
                            'valid_parameters' => ['param5', 'param6'],
                        ],
                        'large' => [
                            'name' => 'Disabled',
                            'enabled' => false,
                            'item_view_types' => [],
                            'valid_parameters' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.view_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsItemViewTypesMerge()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'view_types' => [
                            'default' => [
                                'name' => 'Default',
                                'enabled' => true,
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'block_definitions' => [
                    'block' => [
                        'view_types' => [
                            'default' => [
                                'name' => 'Default',
                                'enabled' => true,
                                'item_view_types' => [
                                    'other' => [
                                        'name' => 'Other',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ],
                                'other' => [
                                    'name' => 'Other',
                                    'enabled' => true,
                                ],
                            ],
                            'valid_parameters' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.view_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithoutValidParameters()
    {
        $config = [
            [
                'block_definitions' => [
                    'block' => [
                        'view_types' => [
                            'default' => [
                                'name' => 'Default',
                                'enabled' => true,
                                'item_view_types' => [
                                    'standard' => [
                                        'name' => 'Standard',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block' => [
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'enabled' => true,
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                    'enabled' => true,
                                ],
                            ],
                            'valid_parameters' => null,
                        ],
                    ],
                ],
            ],
        ];

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
        $config = [
            'block_definitions' => [
                'block' => [
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEmptyIcon()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'icon' => '',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNonStringIcon()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'icon' => 42,
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoBlockDefinitions()
    {
        $config = [
            'block_definitions' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithEmptyCollection()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'collections' => [
                        'default' => [
                            'valid_item_types' => [],
                            'valid_query_types' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Collections need to allow at least one item type or at least one query type.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithMissingContentForm()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'forms' => [
                        'full' => [
                            'enabled' => false,
                        ],
                        'design' => [
                            'enabled' => true,
                        ],
                    ],
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithMissingDesignForm()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'forms' => [
                        'full' => [
                            'enabled' => false,
                        ],
                        'content' => [
                            'enabled' => true,
                        ],
                    ],
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithFullAndDesignForm()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'forms' => [
                        'full' => [
                            'enabled' => true,
                        ],
                        'design' => [
                            'enabled' => true,
                        ],
                    ],
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithFullAndContentForm()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'forms' => [
                        'full' => [
                            'enabled' => true,
                        ],
                        'content' => [
                            'enabled' => true,
                        ],
                    ],
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithFullAndDesignAndContentForm()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'forms' => [
                        'full' => [
                            'enabled' => true,
                        ],
                        'design' => [
                            'enabled' => true,
                        ],
                        'content' => [
                            'enabled' => true,
                        ],
                    ],
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'item_view_types' => [
                                'standard' => [
                                    'name' => 'Standard',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoViewTypes()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'view_types' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode::getConfigurationNode
     */
    public function testBlockDefinitionSettingsWithNoItemViewTypes()
    {
        $config = [
            'block_definitions' => [
                'block' => [
                    'name' => 'Block',
                    'view_types' => [
                        'default' => [
                            'name' => 'Default',
                            'item_view_types' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
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

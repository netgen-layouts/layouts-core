<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockDefinitionNode;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use Netgen\Layouts\Block\Form\FullEditType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[CoversClass(BlockDefinitionNode::class)]
#[CoversClass(Configuration::class)]
final class BlockDefinitionNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testBlockDefinitionSettings(): void
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
                        'defaults' => [
                            'name' => 'Name',
                            'view_type' => 'large',
                            'item_view_type' => 'standard',
                            'parameters' => [
                                'param1' => 'value1',
                                'param2' => 'value2',
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
                    'defaults' => [
                        'name' => 'Name',
                        'view_type' => 'large',
                        'item_view_type' => 'standard',
                        'parameters' => [
                            'param1' => 'value1',
                            'param2' => 'value2',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions',
        );
    }

    public function testBlockDefinitionSettingsWithNoIcon(): void
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
            'block_definitions.*.icon',
        );
    }

    public function testBlockDefinitionSettingsWithNullIcon(): void
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
            'block_definitions.*.icon',
        );
    }

    public function testBlockDefinitionSettingsWithNoHandler(): void
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
            'block_definitions.*.handler',
        );
    }

    public function testBlockDefinitionSettingsWithDisabledBlockDefinition(): void
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
            'block_definitions.*.enabled',
        );
    }

    public function testBlockDefinitionSettingsWithNoTranslatableConfig(): void
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
            'block_definitions.*.translatable',
        );
    }

    public function testBlockDefinitionSettingsWithEnabledTranslatableConfig(): void
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
            'block_definitions.*.translatable',
        );
    }

    public function testBlockDefinitionSettingsWithNoCollections(): void
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
            'block_definitions.*.collections',
        );
    }

    public function testBlockDefinitionSettingsWithDefaultCollectionConfig(): void
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
            'block_definitions.*.collections',
        );
    }

    public function testBlockDefinitionSettingsWithValidQueryTypes(): void
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
            'block_definitions.*.collections.default.valid_query_types',
        );
    }

    public function testBlockDefinitionSettingsWithAllValidQueryTypes(): void
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
            'block_definitions.*.collections.default.valid_query_types',
        );
    }

    public function testBlockDefinitionSettingsWithEmptyValidQueryTypes(): void
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
            'block_definitions.*.collections.default.valid_query_types',
        );
    }

    public function testBlockDefinitionSettingsWithValidItemTypes(): void
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
            'block_definitions.*.collections.default.valid_item_types',
        );
    }

    public function testBlockDefinitionSettingsWithAllValidItemTypes(): void
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
            'block_definitions.*.collections.default.valid_item_types',
        );
    }

    public function testBlockDefinitionSettingsWithEmptyValidItemTypes(): void
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
            'block_definitions.*.collections.default.valid_item_types',
        );
    }

    public function testBlockDefinitionSettingsWithDesignAndContentForms(): void
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
            'block_definitions.*.forms',
        );
    }

    public function testBlockDefinitionSettingsViewTypesMerge(): void
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
            'block_definitions.*.view_types',
        );
    }

    public function testBlockDefinitionSettingsItemViewTypesMerge(): void
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
            'block_definitions.*.view_types',
        );
    }

    public function testBlockDefinitionSettingsWithoutValidParameters(): void
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
            'block_definitions.*.view_types.*',
        );
    }

    public function testBlockDefinitionSettingsWithNoName(): void
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

    public function testBlockDefinitionSettingsWithEmptyIcon(): void
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

    public function testBlockDefinitionSettingsWithNonStringIcon(): void
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

    public function testBlockDefinitionSettingsWithNoBlockDefinitions(): void
    {
        $config = [
            'block_definitions' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    public function testBlockDefinitionSettingsWithEmptyCollection(): void
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

    public function testBlockDefinitionSettingsWithMissingContentForm(): void
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

    public function testBlockDefinitionSettingsWithMissingDesignForm(): void
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

    public function testBlockDefinitionSettingsWithFullAndDesignForm(): void
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

    public function testBlockDefinitionSettingsWithFullAndContentForm(): void
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

    public function testBlockDefinitionSettingsWithFullAndDesignAndContentForm(): void
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

    public function testBlockDefinitionSettingsWithNoViewTypes(): void
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

    public function testBlockDefinitionSettingsWithNoItemViewTypes(): void
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

    public function testBlockDefinitionSettingsWithNoDefaultName(): void
    {
        $config = [
            [
                'block_definitions' => [
                    'block_definition' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block_definition' => [
                    'defaults' => [
                        'name' => '',
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.defaults.name',
        );
    }

    public function testBlockDefinitionSettingsWithNoDefaultViewType(): void
    {
        $config = [
            [
                'block_definitions' => [
                    'block_definition' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block_definition' => [
                    'defaults' => [
                        'view_type' => '',
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.defaults.view_type',
        );
    }

    public function testBlockDefinitionSettingsWithNoDefaultItemViewType(): void
    {
        $config = [
            [
                'block_definitions' => [
                    'block_definition' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block_definition' => [
                    'defaults' => [
                        'item_view_type' => '',
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.defaults.item_view_type',
        );
    }

    public function testBlockDefinitionSettingsWithNoDefaultParameters(): void
    {
        $config = [
            [
                'block_definitions' => [
                    'block_definition' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block_definition' => [
                    'defaults' => [
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.defaults.parameters',
        );
    }

    public function testBlockDefinitionSettingsWithNoParametersMerge(): void
    {
        $config = [
            [
                'block_definitions' => [
                    'block_definition' => [
                        'defaults' => [
                            'parameters' => [
                                'param1' => 'value1',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'block_definitions' => [
                    'block_definition' => [
                        'defaults' => [
                            'parameters' => [
                                'param2' => 'value2',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block_definition' => [
                    'defaults' => [
                        'parameters' => [
                            'param2' => 'value2',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.defaults.parameters',
        );
    }

    public function testBlockDefinitionSettingsWithNoDefaults(): void
    {
        $config = [
            [
                'block_definitions' => [
                    'block_definition' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_definitions' => [
                'block_definition' => [
                    'defaults' => [
                        'name' => '',
                        'view_type' => '',
                        'item_view_type' => '',
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_definitions.*.defaults',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}

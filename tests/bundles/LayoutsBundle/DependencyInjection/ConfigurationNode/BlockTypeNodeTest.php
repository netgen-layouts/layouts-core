<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class BlockTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettings(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'name' => 'Block type',
                        'icon' => '/icon.svg',
                        'definition_identifier' => 'title',
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
            'block_types' => [
                'block_type' => [
                    'name' => 'Block type',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'definition_identifier' => 'title',
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
            'block_types',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoIcon(): void
    {
        $config = [
            [
                'block_types' => [
                    'block' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.icon',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNullIcon(): void
    {
        $config = [
            [
                'block_types' => [
                    'block' => [
                        'icon' => null,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.icon',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoName(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.name',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoNameAndWithDefinitionIdentifierWhenDisabled(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'enabled' => false,
                        'icon' => '/icon.svg',
                        'definition_identifier' => 'title',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [
                    'enabled' => false,
                    'icon' => '/icon.svg',
                    'definition_identifier' => 'title',
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
            'block_types.*',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefinitionIdentifier(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.definition_identifier',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultName(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [
                    'defaults' => [
                        'name' => '',
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.name',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultViewType(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [
                    'defaults' => [
                        'view_type' => '',
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.view_type',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultItemViewType(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [
                    'defaults' => [
                        'item_view_type' => '',
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.item_view_type',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaultParameters(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'defaults' => [],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [
                    'defaults' => [
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_types.*.defaults.parameters',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoParametersMerge(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [
                        'defaults' => [
                            'parameters' => [
                                'param1' => 'value1',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'block_types' => [
                    'block_type' => [
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
            'block_types' => [
                'block_type' => [
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
            'block_types.*.defaults.parameters',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaults(): void
    {
        $config = [
            [
                'block_types' => [
                    'block_type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'block_types' => [
                'block_type' => [
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
            'block_types.*.defaults',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeWithEmptyIcon(): void
    {
        $config = [
            'block_types' => [
                'block' => [
                    'name' => 'Block',
                    'icon' => '',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeWithNonStringIcon(): void
    {
        $config = [
            'block_types' => [
                'block' => [
                    'name' => 'Block',
                    'icon' => 42,
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoBlockTypes(): void
    {
        $config = [
            'block_types' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoNameAndWithDefinitionIdentifier(): void
    {
        $config = [
            'block_types' => [
                'block_type' => [
                    'definition_identifier' => 'title',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}

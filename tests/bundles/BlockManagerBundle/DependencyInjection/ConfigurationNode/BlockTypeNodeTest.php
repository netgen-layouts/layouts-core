<?php

declare(strict_types=1);

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
            'block_types.*.defaults.parameters'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoDefaults()
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
            'block_types.*.defaults'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeWithEmptyIcon()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeWithNonStringIcon()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoBlockTypes()
    {
        $config = [
            'block_types' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeNode::getConfigurationNode
     */
    public function testBlockTypeSettingsWithNoNameAndWithDefinitionIdentifier()
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

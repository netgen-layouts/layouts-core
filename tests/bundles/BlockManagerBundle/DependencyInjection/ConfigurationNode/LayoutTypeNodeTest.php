<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class LayoutTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettings()
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'name' => 'layout',
                        'icon' => '/icon.svg',
                        'zones' => [
                            'zone' => [
                                'name' => 'zone',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'icon' => '/icon.svg',
                    'enabled' => true,
                    'zones' => [
                        'zone' => [
                            'name' => 'zone',
                            'allowed_block_definitions' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoIcon()
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNullIcon()
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'icon' => null,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'icon' => null,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.icon'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsNoZonesMerge()
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'left' => [
                                'name' => 'Left',
                            ],
                            'right' => [
                                'name' => 'Right',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'top' => [
                                'name' => 'Top',
                            ],
                            'bottom' => [
                                'name' => 'Bottom',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'zones' => [
                        'top' => [
                            'name' => 'Top',
                            'allowed_block_definitions' => [],
                        ],
                        'bottom' => [
                            'name' => 'Bottom',
                            'allowed_block_definitions' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithAllowedBlockDefinitions()
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'zone' => [
                                'allowed_block_definitions' => ['title', 'text'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'zones' => [
                        'zone' => [
                            'allowed_block_definitions' => ['title', 'text'],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'layout_types.*.zones.*.allowed_block_definitions'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNonUniqueAllowedBlockDefinitions()
    {
        $config = [
            [
                'layout_types' => [
                    'layout' => [
                        'zones' => [
                            'zone' => [
                                'allowed_block_definitions' => ['title', 'text', 'title'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'layout_types' => [
                'layout' => [
                    'zones' => [
                        'zone' => [
                            'allowed_block_definitions' => ['title', 'text'],
                        ],
                    ],
                ],
            ],
        ];

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
        $config = ['layout_types' => []];
        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoName()
    {
        $config = ['layout_types' => ['layout' => []]];
        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoZones()
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeWithEmptyIcon()
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'Layout',
                    'icon' => '',
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeWithNonStringIcon()
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'Layout',
                    'icon' => 42,
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config], 'Icon path needs to be a non empty string or null.');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithEmptyZones()
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'zones' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithNoZoneName()
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'zones' => [],
                ],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\LayoutTypeNode::getConfigurationNode
     */
    public function testLayoutTypeSettingsWithEmptyAllowedBlockDefinitions()
    {
        $config = [
            'layout_types' => [
                'layout' => [
                    'name' => 'layout',
                    'zones' => [
                        'name' => 'zone',
                        'allowed_block_definitions' => [],
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

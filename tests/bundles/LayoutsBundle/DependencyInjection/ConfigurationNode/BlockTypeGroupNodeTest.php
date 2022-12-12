<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class BlockTypeGroupNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettings(): void
    {
        $config = [
            [
                'block_type_groups' => [
                    'block_type_group' => [
                        'name' => 'block_type_group',
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_type_groups' => [
                'block_type_group' => [
                    'name' => 'block_type_group',
                    'enabled' => true,
                    'priority' => 0,
                    'block_types' => [],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithPriority(): void
    {
        $config = [
            [
                'block_type_groups' => [
                    'block_type_group' => [
                        'priority' => 42,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_type_groups' => [
                'block_type_group' => [
                    'priority' => 42,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.priority',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension::load
     */
    public function testBlockTypeGroupsSettingsWithBlockTypesMerge(): void
    {
        $config = [
            [
                'block_type_groups' => [
                    'block_type_group' => [
                        'block_types' => ['title', 'text'],
                    ],
                ],
            ],
            [
                'block_type_groups' => [
                    'block_type_group' => [
                        'block_types' => ['image'],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_type_groups' => [
                'block_type_group' => [
                    'block_types' => [
                        [
                            'identifier' => 'title',
                            'priority' => 0,
                        ],
                        [
                            'identifier' => 'text',
                            'priority' => 0,
                        ],
                        [
                            'identifier' => 'image',
                            'priority' => 0,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithBlockTypes(): void
    {
        $config = [
            [
                'block_type_groups' => [
                    'block_type_group' => [
                        'block_types' => ['title', 'image'],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_type_groups' => [
                'block_type_group' => [
                    'block_types' => [
                        [
                            'identifier' => 'title',
                            'priority' => 0,
                        ],
                        [
                            'identifier' => 'image',
                            'priority' => 0,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoBlockTypeSettings(): void
    {
        $config = [
            'block_type_groups' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoName(): void
    {
        $config = [
            'block_type_groups' => [
                'block_type_group' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithEmptyBlockTypes(): void
    {
        $config = [
            'block_type_groups' => [
                'block_type_group' => [
                    'name' => 'block_type_group',
                    'block_types' => [],
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

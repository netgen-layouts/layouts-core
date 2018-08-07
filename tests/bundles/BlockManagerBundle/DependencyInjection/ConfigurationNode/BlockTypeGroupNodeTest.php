<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class BlockTypeGroupNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
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
                    'block_types' => [],
                ],
            ],
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension::load
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
                    'block_types' => ['title', 'text', 'image'],
                ],
            ],
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
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
                    'block_types' => ['title', 'image'],
                ],
            ],
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNonUniqueBlockTypes(): void
    {
        $config = [
            [
                'block_type_groups' => [
                    'block_type_group' => [
                        'block_types' => ['title', 'image', 'title'],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'block_type_groups' => [
                'block_type_group' => [
                    'block_types' => ['title', 'image'],
                ],
            ],
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoBlockTypeSettings(): void
    {
        $config = [
            'block_type_groups' => [],
        ];

        self::assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoName(): void
    {
        $config = [
            'block_type_groups' => [
                'block_type_group' => [],
            ],
        ];

        self::assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
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

        self::assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}

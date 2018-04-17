<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class BlockTypeGroupNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettings()
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

        $this->assertProcessedConfigurationEquals(
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
    public function testBlockTypeGroupsSettingsWithBlockTypesMerge()
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

        $this->assertProcessedConfigurationEquals(
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
    public function testBlockTypeGroupsSettingsWithBlockTypes()
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

        $this->assertProcessedConfigurationEquals(
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
    public function testBlockTypeGroupsSettingsWithNonUniqueBlockTypes()
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

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'block_type_groups.*.block_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoBlockTypeSettings()
    {
        $config = [
            'block_type_groups' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithNoName()
    {
        $config = [
            'block_type_groups' => [
                'block_type_group' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\BlockTypeGroupNode::getConfigurationNode
     */
    public function testBlockTypeGroupsSettingsWithEmptyBlockTypes()
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

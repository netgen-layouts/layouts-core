<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class ItemsNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testItemsSettings()
    {
        $config = [
            [
                'items' => [
                    'value_types' => [
                        'value1' => [
                            'name' => 'Value 1',
                        ],
                        'value2' => [
                            'enabled' => false,
                            'name' => 'Value 2',
                        ],
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'items' => [
                'value_types' => [
                    'value1' => [
                        'name' => 'Value 1',
                        'enabled' => true,
                    ],
                    'value2' => [
                        'name' => 'Value 2',
                        'enabled' => false,
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'items.value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testItemsSettingsWithNoValueTypes()
    {
        $config = [['items' => []]];

        $expectedConfig = [
            'items' => [
                'value_types' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'items.value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testItemsSettingsWithEmptyValueTypes()
    {
        $config = [['items' => ['value_types' => []]]];

        $expectedConfig = [
            'items' => [
                'value_types' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'items.value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ItemsNode::getConfigurationNode
     */
    public function testValueTypesSettingsWithNoName()
    {
        $config = [['items' => ['value_types' => ['value' => []]]]];
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

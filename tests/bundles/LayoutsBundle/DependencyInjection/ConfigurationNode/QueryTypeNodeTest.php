<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class QueryTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettings(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [
                        'name' => 'Type',
                        'handler' => 'handler',
                        'priority' => 100,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'query_types' => [
                'type' => [
                    'enabled' => true,
                    'name' => 'Type',
                    'handler' => 'handler',
                    'priority' => 100,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithDefaultPriority(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'query_types' => [
                'type' => [
                    'priority' => 0,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.priority',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoHandler(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [],
                ],
            ],
        ];

        $expectedConfig = [
            'query_types' => [
                'type' => [],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.handler',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoQueryTypes(): void
    {
        $config = [
            'query_types' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoName(): void
    {
        $config = [
            'query_types' => [
                'type' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithEmptyName(): void
    {
        $config = [
            'query_types' => [
                'type' => [
                    'name' => '',
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

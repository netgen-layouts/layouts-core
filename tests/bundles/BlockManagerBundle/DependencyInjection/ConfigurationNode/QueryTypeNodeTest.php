<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class QueryTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettings(): void
    {
        $config = [
            [
                'query_types' => [
                    'type' => [
                        'name' => 'Type',
                        'handler' => 'handler',
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
                ],
            ],
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
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

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'query_types.*.handler'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoQueryTypes(): void
    {
        $config = [
            'query_types' => [],
        ];

        self::assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoName(): void
    {
        $config = [
            'query_types' => [
                'type' => [],
            ],
        ];

        self::assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
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

        self::assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;

final class QueryTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettings()
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

        $this->assertProcessedConfigurationEquals(
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
    public function testQueryTypeSettingsWithNoHandler()
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
            'query_types.*.handler'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoQueryTypes()
    {
        $config = [
            'query_types' => [],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithNoName()
    {
        $config = [
            'query_types' => [
                'type' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid([$config]);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\QueryTypeNode::getConfigurationNode
     */
    public function testQueryTypeSettingsWithEmptyName()
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

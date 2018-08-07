<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\NetgenBlockManagerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ValueTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ValueTypeNode::getConfigurationNode
     */
    public function testValueTypeSettings(): void
    {
        $config = [
            [
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
        ];

        $expectedConfig = [
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
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ValueTypeNode::getConfigurationNode
     */
    public function testValueTypeSettingsWithNoValueTypes(): void
    {
        $config = [[]];

        $expectedConfig = [
            'value_types' => [],
        ];

        self::assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'value_types'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ConfigurationNode\ValueTypeNode::getConfigurationNode
     */
    public function testValueTypesSettingsWithNoName(): void
    {
        $config = [['value_types' => ['value' => []]]];
        self::assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenBlockManagerExtension());
    }
}

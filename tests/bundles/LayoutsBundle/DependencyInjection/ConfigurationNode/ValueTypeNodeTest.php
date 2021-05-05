<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ValueTypeNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\ValueTypeNode::getConfigurationNode
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
                        'manual_items' => false,
                    ],
                ],
            ],
        ];

        $expectedConfig = [
            'value_types' => [
                'value1' => [
                    'name' => 'Value 1',
                    'enabled' => true,
                    'manual_items' => true,
                ],
                'value2' => [
                    'name' => 'Value 2',
                    'enabled' => false,
                    'manual_items' => false,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'value_types',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\ValueTypeNode::getConfigurationNode
     */
    public function testValueTypeSettingsWithNoValueTypes(): void
    {
        $config = [[]];

        $expectedConfig = [
            'value_types' => [],
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'value_types',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\ValueTypeNode::getConfigurationNode
     */
    public function testValueTypesSettingsWithNoName(): void
    {
        $config = [['value_types' => ['value' => []]]];
        $this->assertConfigurationIsInvalid([$config]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}

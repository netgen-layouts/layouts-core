<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\ConfigurationNode;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\NetgenLayoutsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DebugNodeTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DebugNode::getConfigurationNode
     */
    public function testDebugSettings(): void
    {
        $config = [
            [
                'debug' => true,
            ],
        ];

        $expectedConfig = [
            'debug' => true,
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'debug',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\Configuration::getNodes
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DebugNode::getConfigurationNode
     */
    public function testDefaultDebugSettings(): void
    {
        $config = [
            [],
        ];

        $expectedConfig = [
            'debug' => false,
        ];

        $this->assertProcessedConfigurationEquals(
            $config,
            $expectedConfig,
            'debug',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\ConfigurationNode\DebugNode::getConfigurationNode
     */
    public function testDebugSettingsWithInvalidDebugConfig(): void
    {
        $config = [
            [
                'debug' => [],
            ],
        ];

        $this->assertConfigurationIsInvalid(
            $config,
            '/Invalid type for path "netgen_layouts.debug". Expected "?bool(ean)?"?, but got "?array"?./',
            true,
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration(new NetgenLayoutsExtension());
    }
}

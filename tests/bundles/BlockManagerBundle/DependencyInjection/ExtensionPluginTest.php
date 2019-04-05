<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\Stubs\ConfigurationNode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class ExtensionPluginTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin
     */
    private $plugin;

    public function setUp(): void
    {
        $this->plugin = $this->getMockForAbstractClass(ExtensionPlugin::class);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::preProcessConfiguration
     */
    public function testPreProcessConfiguration(): void
    {
        $processedConfig = $this->plugin->preProcessConfiguration([]);
        self::assertSame([], $processedConfig);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::postProcessConfiguration
     */
    public function testPostProcessConfiguration(): void
    {
        $processedConfig = $this->plugin->postProcessConfiguration([]);
        self::assertSame([], $processedConfig);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::addConfiguration
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::getConfigurationNodes
     */
    public function testAddConfiguration(): void
    {
        $node1 = new ConfigurationNode();
        $node2 = new ConfigurationNode();

        $this->plugin = $this->getMockForAbstractClass(
            ExtensionPlugin::class,
            [],
            '',
            true,
            true,
            true,
            ['getConfigurationNodes']
        );

        $this->plugin
            ->expects(self::once())
            ->method('getConfigurationNodes')
            ->willReturn([$node1, $node2]);

        $rootNodeMock = $this->createMock(ArrayNodeDefinition::class);
        $nodeBuilderMock = $this->createMock(NodeBuilder::class);

        $rootNodeMock
            ->expects(self::at(0))
            ->method('children')
            ->willReturn($nodeBuilderMock);

        $nodeBuilderMock
            ->expects(self::at(0))
            ->method('append')
            ->with(self::equalTo($node1->getConfigurationNode()));

        $nodeBuilderMock
            ->expects(self::at(1))
            ->method('append')
            ->with(self::equalTo($node2->getConfigurationNode()));

        $this->plugin->addConfiguration($rootNodeMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\ExtensionPlugin::appendConfigurationFiles
     */
    public function testAppendConfigurationFiles(): void
    {
        self::assertSame([], $this->plugin->appendConfigurationFiles());
    }
}

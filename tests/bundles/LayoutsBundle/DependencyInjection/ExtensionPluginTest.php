<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\ConfigurationNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

#[CoversClass(ExtensionPlugin::class)]
final class ExtensionPluginTest extends TestCase
{
    private MockObject&ExtensionPlugin $plugin;

    protected function setUp(): void
    {
        $this->plugin = $this->getMockForAbstractClass(ExtensionPlugin::class);
    }

    public function testPreProcessConfiguration(): void
    {
        $processedConfig = $this->plugin->preProcessConfiguration([]);
        self::assertSame([], $processedConfig);
    }

    public function testPostProcessConfiguration(): void
    {
        $processedConfig = $this->plugin->postProcessConfiguration([]);
        self::assertSame([], $processedConfig);
    }

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
            ['getConfigurationNodes'],
        );

        $this->plugin
            ->expects(self::once())
            ->method('getConfigurationNodes')
            ->willReturn([$node1, $node2]);

        $rootNodeMock = $this->createMock(ArrayNodeDefinition::class);
        $nodeBuilderMock = $this->createMock(NodeBuilder::class);

        $rootNodeMock
            ->method('children')
            ->willReturn($nodeBuilderMock);

        $nodeBuilderMock
            ->method('append')
            ->with(self::equalTo($node1->getConfigurationNode()));

        $nodeBuilderMock
            ->method('append')
            ->with(self::equalTo($node2->getConfigurationNode()));

        $this->plugin->addConfiguration($rootNodeMock);
    }

    public function testAppendConfigurationFiles(): void
    {
        self::assertSame([], $this->plugin->appendConfigurationFiles());
    }
}

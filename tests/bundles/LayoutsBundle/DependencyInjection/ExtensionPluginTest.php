<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection;

use Netgen\Bundle\LayoutsBundle\DependencyInjection\ExtensionPlugin;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\ConfigurationNode;
use Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\Stubs\EmptyExtensionPlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

#[CoversClass(ExtensionPlugin::class)]
final class ExtensionPluginTest extends TestCase
{
    private EmptyExtensionPlugin $plugin;

    protected function setUp(): void
    {
        $this->plugin = new EmptyExtensionPlugin();
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
        $node = new ConfigurationNode();

        $rootNodeMock = $this->createMock(ArrayNodeDefinition::class);
        $nodeBuilderMock = $this->createMock(NodeBuilder::class);

        $rootNodeMock
            ->method('children')
            ->willReturn($nodeBuilderMock);

        $nodeBuilderMock
            ->method('append')
            ->with(self::equalTo($node->getConfigurationNode()));

        $nodeBuilderMock
            ->method('append')
            ->with(self::equalTo($node->getConfigurationNode()));

        $this->plugin->addConfiguration($rootNodeMock);
    }

    public function testAppendConfigurationFiles(): void
    {
        self::assertSame([], $this->plugin->appendConfigurationFiles());
    }
}

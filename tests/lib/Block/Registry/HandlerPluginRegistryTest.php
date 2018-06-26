<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

final class HandlerPluginRegistryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::addPlugin
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistry(): void
    {
        $handlerPlugin = HandlerPlugin::instance([BlockDefinitionHandler::class]);

        $registry = new HandlerPluginRegistry([$handlerPlugin]);

        $this->assertSame(
            [$handlerPlugin],
            $registry->getPlugins(BlockDefinitionHandler::class)
        );

        $this->assertSame(
            [],
            $registry->getPlugins(stdClass::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::addPlugin
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistryWithInterface(): void
    {
        $handlerPlugin = HandlerPlugin::instance([BlockDefinitionHandlerInterface::class]);

        $registry = new HandlerPluginRegistry([$handlerPlugin]);

        $this->assertSame(
            [$handlerPlugin],
            $registry->getPlugins(BlockDefinitionHandler::class)
        );

        $this->assertSame(
            [],
            $registry->getPlugins(stdClass::class)
        );
    }
}

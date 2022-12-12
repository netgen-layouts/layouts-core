<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Registry;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\Registry\HandlerPluginRegistry;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\Layouts\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

final class HandlerPluginRegistryTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Block\Registry\HandlerPluginRegistry::__construct
     * @covers \Netgen\Layouts\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistry(): void
    {
        $handlerPlugin = HandlerPlugin::instance([BlockDefinitionHandler::class]);

        $registry = new HandlerPluginRegistry([$handlerPlugin]);

        self::assertSame(
            [$handlerPlugin],
            $registry->getPlugins('definition', BlockDefinitionHandler::class),
        );

        self::assertSame(
            [],
            $registry->getPlugins('definition', stdClass::class),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistryWithInterface(): void
    {
        $handlerPlugin = HandlerPlugin::instance([BlockDefinitionHandlerInterface::class]);

        $registry = new HandlerPluginRegistry([$handlerPlugin]);

        self::assertSame(
            [$handlerPlugin],
            $registry->getPlugins('definition', BlockDefinitionHandler::class),
        );

        self::assertSame(
            [],
            $registry->getPlugins('definition', stdClass::class),
        );
    }
}

<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Handler\ListHandler;
use Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

class HandlerPluginRegistryTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new HandlerPluginRegistry();
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::addPlugin
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistry()
    {
        $handlerPlugin = HandlerPlugin::instance(array(TitleHandler::class, ListHandler::class));

        $this->registry->addPlugin($handlerPlugin);

        $this->assertEquals(
            array($handlerPlugin),
            $this->registry->getPlugins(TitleHandler::class)
        );

        $this->assertEquals(
            array($handlerPlugin),
            $this->registry->getPlugins(ListHandler::class)
        );

        $this->assertEquals(
            array(),
            $this->registry->getPlugins(stdClass::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::addPlugin
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistryWithInterface()
    {
        $handlerPlugin = HandlerPlugin::instance(array(BlockDefinitionHandlerInterface::class));

        $this->registry->addPlugin($handlerPlugin);

        $this->assertEquals(
            array($handlerPlugin),
            $this->registry->getPlugins(TitleHandler::class)
        );

        $this->assertEquals(
            array($handlerPlugin),
            $this->registry->getPlugins(ListHandler::class)
        );

        $this->assertEquals(
            array(),
            $this->registry->getPlugins(stdClass::class)
        );
    }
}

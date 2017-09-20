<?php

namespace Netgen\BlockManager\Tests\Block\Registry;

use Netgen\BlockManager\Block\Registry\HandlerPluginRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
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
        $handlerPlugin = new HandlerPlugin();

        $this->registry->addPlugin($handlerPlugin);

        $this->assertEquals(
            array($handlerPlugin),
            $this->registry->getPlugins(BlockDefinitionHandler::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::addPlugin
     * @covers \Netgen\BlockManager\Block\Registry\HandlerPluginRegistry::getPlugins
     */
    public function testRegistryWithUnknownClass()
    {
        $handlerPlugin = new HandlerPlugin();

        $this->registry->addPlugin($handlerPlugin);

        $this->assertEquals(
            array(),
            $this->registry->getPlugins(stdClass::class)
        );
    }
}

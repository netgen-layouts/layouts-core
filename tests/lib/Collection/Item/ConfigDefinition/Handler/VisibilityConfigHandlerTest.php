<?php

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Handler;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler\VisibilityConfigHandler;
use Netgen\BlockManager\Core\Values\Collection\Item;
use PHPUnit\Framework\TestCase;

final class VisibilityConfigHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    public function setUp()
    {
        $this->handler = new VisibilityConfigHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler\VisibilityConfigHandler::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->handler->isEnabled(new Item()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler\VisibilityConfigHandler::isEnabled
     */
    public function testIsEnabledWithNoItem()
    {
        $this->assertFalse($this->handler->isEnabled($this->createMock(ConfigAwareValue::class)));
    }
}

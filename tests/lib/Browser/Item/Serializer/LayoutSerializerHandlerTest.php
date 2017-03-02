<?php

namespace Netgen\BlockManager\Tests\Browser\Item\Serializer;

use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Browser\Item\Serializer\LayoutSerializerHandler;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use PHPUnit\Framework\TestCase;

class LayoutSerializerHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\Serializer\LayoutSerializerHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new LayoutSerializerHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Serializer\LayoutSerializerHandler::isSelectable
     */
    public function testIsSelectable()
    {
        $this->assertTrue(
            $this->handler->isSelectable($this->getItem())
        );
    }

    /**
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new Layout());
    }
}

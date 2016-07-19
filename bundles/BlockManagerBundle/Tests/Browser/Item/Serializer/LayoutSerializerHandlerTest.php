<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Browser\Item\Serializer;

use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Serializer\LayoutSerializerHandler;
use PHPUnit\Framework\TestCase;

class LayoutSerializerHandlerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\Serializer\LayoutSerializerHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new LayoutSerializerHandler();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Serializer\LayoutSerializerHandler::isSelectable
     */
    public function testIsSelectable()
    {
        self::assertEquals(
            true,
            $this->handler->isSelectable($this->getItem())
        );
    }

    /**
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new LayoutInfo());
    }
}

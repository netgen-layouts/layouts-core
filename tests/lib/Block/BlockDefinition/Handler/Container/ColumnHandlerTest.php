<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler\Container;

use Netgen\BlockManager\Block\BlockDefinition\Handler\Container\ColumnHandler;
use PHPUnit\Framework\TestCase;

class ColumnHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\Container\ColumnHandler
     */
    private $handler;

    public function setUp()
    {
        $this->handler = new ColumnHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Container\ColumnHandler::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers()
    {
        $this->assertEquals(array('main'), $this->handler->getPlaceholderIdentifiers());
    }
}

<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler\Container;

use Netgen\BlockManager\Block\BlockDefinition\Handler\Container\TwoColumnsHandler;
use PHPUnit\Framework\TestCase;

class TwoColumnsHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\Container\TwoColumnsHandler
     */
    private $handler;

    public function setUp()
    {
        $this->handler = new TwoColumnsHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Container\TwoColumnsHandler::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers()
    {
        $this->assertEquals(array('left', 'right'), $this->handler->getPlaceholderIdentifiers());
    }
}

<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler;
use PHPUnit\Framework\TestCase;

class DynamicContainerHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(DynamicContainerHandler::class);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertTrue($this->handler->isDynamicContainer());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers()
    {
        $this->assertEquals(array(), $this->handler->getPlaceholderIdentifiers());
    }
}

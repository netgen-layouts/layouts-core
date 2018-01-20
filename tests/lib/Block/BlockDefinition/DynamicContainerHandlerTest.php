<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler;
use PHPUnit\Framework\TestCase;

final class DynamicContainerHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\DynamicContainerHandler
     */
    private $handler;

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

<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(ContainerDefinitionHandler::class);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertFalse($this->handler->isDynamicContainer());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandler::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers()
    {
        $this->assertNull($this->handler->getPlaceholderIdentifiers());
    }
}

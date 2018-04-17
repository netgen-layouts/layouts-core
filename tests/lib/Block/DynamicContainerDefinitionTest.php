<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\DynamicContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class DynamicContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Block\ContainerDefinition
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->handler = new DynamicContainerDefinitionHandler();

        $this->blockDefinition = new ContainerDefinition(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals([], $this->blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertTrue($this->blockDefinition->isDynamicContainer());
    }
}

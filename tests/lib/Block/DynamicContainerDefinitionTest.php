<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
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
            array(
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'config' => new Configuration(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals(array(), $this->blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertTrue($this->blockDefinition->isDynamicContainer());
    }
}

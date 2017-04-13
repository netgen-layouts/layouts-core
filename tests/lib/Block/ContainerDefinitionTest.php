<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Block\ContainerDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->handler = new ContainerDefinitionHandler();

        $this->configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new ContainerDefinition(
            array(
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'config' => $this->configMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals(array('left', 'right'), $this->blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertFalse($this->blockDefinition->isDynamicContainer());
    }
}

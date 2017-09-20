<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\DynamicContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

class DynamicContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var \Netgen\BlockManager\Block\ContainerDefinition
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->handler = new DynamicContainerDefinitionHandler();

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

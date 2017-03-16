<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\PlaceholderDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\DynamicContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

class DynamicContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
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
        $this->handler = new DynamicContainerDefinitionHandler();

        $this->configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new ContainerDefinition(
            array(
                'identifier' => 'block_definition',
                'dynamicPlaceholder' => new PlaceholderDefinition(array('identifier' => 'dynamic')),
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
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholder
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Container definition is a dynamic container and does not have any placeholders.
     */
    public function testGetPlaceholder()
    {
        $this->blockDefinition->getPlaceholder('left');
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::hasPlaceholder
     */
    public function testHasPlaceholder()
    {
        $this->assertFalse($this->blockDefinition->hasPlaceholder('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getDynamicPlaceholder
     */
    public function testGetDynamicPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'dynamic')),
            $this->blockDefinition->getDynamicPlaceholder()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertTrue($this->blockDefinition->isDynamicContainer());
    }
}

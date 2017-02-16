<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\PlaceholderDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\DynamicContainerBlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

class DynamicContainerBlockDefinitionTest extends TestCase
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
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->handler = new DynamicContainerBlockDefinitionHandler();

        $this->configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'block_definition',
                'dynamicPlaceholder' => new PlaceholderDefinition(array('identifier' => 'dynamic')),
                'handler' => $this->handler,
                'config' => $this->configMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals(array(), $this->blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getPlaceholder
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Block definition is a dynamic container and does not have any placeholders.
     */
    public function testGetPlaceholder()
    {
        $this->blockDefinition->getPlaceholder('left');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasPlaceholder
     */
    public function testHasPlaceholder()
    {
        $this->assertFalse($this->blockDefinition->hasPlaceholder('left'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicPlaceholder
     */
    public function testGetDynamicPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'dynamic')),
            $this->blockDefinition->getDynamicPlaceholder()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isContainer
     */
    public function testIsContainer()
    {
        $this->assertTrue($this->blockDefinition->isContainer());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertTrue($this->blockDefinition->isDynamicContainer());
    }
}

<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Block\PlaceholderDefinition;
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
                'placeholders' => array(
                    'left' => new PlaceholderDefinition(array('identifier' => 'left')),
                    'right' => new PlaceholderDefinition(array('identifier' => 'right')),
                ),
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
        $this->assertEquals(
            array(
                'left' => new PlaceholderDefinition(array('identifier' => 'left')),
                'right' => new PlaceholderDefinition(array('identifier' => 'right')),
            ),
            $this->blockDefinition->getPlaceholders()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholder
     */
    public function testGetPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'left')),
            $this->blockDefinition->getPlaceholder('left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholder
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Placeholder with "unknown" identifier does not exist in block definition.
     */
    public function testGetPlaceholderThrowsInvalidArgumentException()
    {
        $this->blockDefinition->getPlaceholder('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::hasPlaceholder
     */
    public function testHasPlaceholder()
    {
        $this->assertTrue($this->blockDefinition->hasPlaceholder('left'));
        $this->assertFalse($this->blockDefinition->hasPlaceholder('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getDynamicPlaceholder
     */
    public function testGetDynamicPlaceholder()
    {
        $this->assertNull($this->blockDefinition->getDynamicPlaceholder());
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertFalse($this->blockDefinition->isDynamicContainer());
    }
}

<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition;

class BlockDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $handlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->handlerMock = $this->createMock(BlockDefinitionHandlerInterface::class);

        $this->configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new BlockDefinition(
            'block_definition',
            $this->handlerMock,
            $this->configMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('block_definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getParameters
     */
    public function testGetParameters()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('getParameters')
            ->will($this->returnValue(array('params')));

        self::assertEquals(array('params'), $this->blockDefinition->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $this->handlerMock
            ->expects($this->once())
            ->method('getDynamicParameters')
            ->with($this->equalTo(new Block()), $this->equalTo(array('params')))
            ->will($this->returnValue(array('dynamic')));

        self::assertEquals(
            array('dynamic'),
            $this->blockDefinition->getDynamicParameters(new Block(), array('params'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfig
     */
    public function testGetConfig()
    {
        self::assertEquals($this->configMock, $this->blockDefinition->getConfig());
    }
}

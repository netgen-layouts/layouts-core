<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockParamConverter;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Service\BlockService;
use PHPUnit\Framework\TestCase;

class BlockParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\LayoutParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->paramConverter = new BlockParamConverter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals('blockId', $this->paramConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('block', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIBlock::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $block = new Block();

        $this->blockServiceMock
            ->expects($this->once())
            ->method('loadBlock')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        self::assertEquals($block, $this->paramConverter->loadValueObject(42));
    }
}

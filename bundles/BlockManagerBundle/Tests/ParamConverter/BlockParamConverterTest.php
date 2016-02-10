<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Service\BlockService;

class BlockParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        $blockService = $this->getMock(BlockService::class);
        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals('blockId', $blockParamConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $blockService = $this->getMock(BlockService::class);
        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals('block', $blockParamConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $blockService = $this->getMock(BlockService::class);
        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals(APIBlock::class, $blockParamConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $block = new Block();

        $blockService = $this->getMock(BlockService::class);
        $blockService
            ->expects($this->once())
            ->method('loadBlock')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals($block, $blockParamConverter->loadValueObject(42, Layout::STATUS_DRAFT));
    }
}

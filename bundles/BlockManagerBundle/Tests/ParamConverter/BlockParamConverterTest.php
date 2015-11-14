<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter;
use Netgen\BlockManager\Core\Values\Page\Block;
use PHPUnit_Framework_TestCase;

class BlockParamConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        $blockService = $this->getMock('Netgen\BlockManager\API\Service\BlockService');
        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals('blockId', $blockParamConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $blockService = $this->getMock('Netgen\BlockManager\API\Service\BlockService');
        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals('block', $blockParamConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $blockService = $this->getMock('Netgen\BlockManager\API\Service\BlockService');
        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals('Netgen\BlockManager\API\Values\Page\Block', $blockParamConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\BlockParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $block = new Block();

        $blockService = $this->getMock('Netgen\BlockManager\API\Service\BlockService');
        $blockService
            ->expects($this->once())
            ->method('loadBlock')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        $blockParamConverter = new BlockParamConverter($blockService);

        self::assertEquals($block, $blockParamConverter->loadValueObject(42));
    }
}

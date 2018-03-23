<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter;
use PHPUnit\Framework\TestCase;

final class BlockParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $blockServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter
     */
    private $paramConverter;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->paramConverter = new BlockParamConverter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('blockId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('block', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIBlock::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter::loadValue
     */
    public function testLoadValue()
    {
        $block = new Block();

        $this->blockServiceMock
            ->expects($this->once())
            ->method('loadBlock')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        $this->assertEquals(
            $block,
            $this->paramConverter->loadValue(
                array(
                    'blockId' => 42,
                    'published' => true,
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\BlockParamConverter::loadValue
     */
    public function testLoadValueDraft()
    {
        $block = new Block();

        $this->blockServiceMock
            ->expects($this->once())
            ->method('loadBlockDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        $this->assertEquals(
            $block,
            $this->paramConverter->loadValue(
                array(
                    'blockId' => 42,
                    'published' => false,
                )
            )
        );
    }
}

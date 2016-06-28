<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter;
use Netgen\BlockManager\Core\Values\Page\BlockDraft;
use Netgen\BlockManager\API\Values\Page\BlockDraft as APIBlockDraft;
use Netgen\BlockManager\API\Service\BlockService;
use PHPUnit\Framework\TestCase;

class BlockDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->paramConverter = new BlockDraftParamConverter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('blockId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('block', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIBlockDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\BlockDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $block = new BlockDraft();

        $this->blockServiceMock
            ->expects($this->once())
            ->method('loadBlockDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        self::assertEquals($block, $this->paramConverter->loadValueObject(array('blockId' => 42)));
    }
}

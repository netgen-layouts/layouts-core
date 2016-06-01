<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter;
use Netgen\BlockManager\Core\Values\Collection\ItemDraft;
use Netgen\BlockManager\API\Values\Collection\ItemDraft as APIItemDraft;
use Netgen\BlockManager\API\Service\CollectionService;

class ItemDraftParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->getMock(CollectionService::class);

        $this->paramConverter = new ItemDraftParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals('itemId', $this->paramConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('item', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIItemDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $item = new ItemDraft();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadItemDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($item));

        self::assertEquals($item, $this->paramConverter->loadValueObject(42));
    }
}

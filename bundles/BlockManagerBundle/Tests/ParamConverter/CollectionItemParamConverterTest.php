<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Service\CollectionService;

class CollectionItemParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->getMock(CollectionService::class);

        $this->paramConverter = new CollectionItemParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals('itemId', $this->paramConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('item', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIItem::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionItemParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $item = new Item();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue($item));

        self::assertEquals($item, $this->paramConverter->loadValueObject(42));
    }
}

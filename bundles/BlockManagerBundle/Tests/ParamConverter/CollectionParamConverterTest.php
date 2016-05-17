<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Service\CollectionService;

class CollectionParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->getMock(CollectionService::class);

        $this->paramConverter = new CollectionParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals('collectionId', $this->paramConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('collection', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APICollection::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $collection = new Collection();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollection')
            ->with($this->equalTo(42), $this->equalTo(APICollection::STATUS_DRAFT))
            ->will($this->returnValue($collection));

        self::assertEquals($collection, $this->paramConverter->loadValueObject(42, APICollection::STATUS_DRAFT));
    }
}

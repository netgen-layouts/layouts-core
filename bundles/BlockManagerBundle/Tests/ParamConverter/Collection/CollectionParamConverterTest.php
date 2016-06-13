<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Service\CollectionService;
use PHPUnit\Framework\TestCase;

class CollectionParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new CollectionParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('collectionId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('collection', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APICollection::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $collection = new Collection();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollection')
            ->with($this->equalTo(42))
            ->will($this->returnValue($collection));

        self::assertEquals($collection, $this->paramConverter->loadValueObject(array('collectionId' => 42)));
    }
}

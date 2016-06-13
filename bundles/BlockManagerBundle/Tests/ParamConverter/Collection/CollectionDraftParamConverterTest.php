<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter;
use Netgen\BlockManager\Core\Values\Collection\CollectionDraft;
use Netgen\BlockManager\API\Values\Collection\CollectionDraft as APICollectionDraft;
use Netgen\BlockManager\API\Service\CollectionService;
use PHPUnit\Framework\TestCase;

class CollectionDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new CollectionDraftParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('collectionId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('collection', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APICollectionDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $collection = new CollectionDraft();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollectionDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($collection));

        self::assertEquals($collection, $this->paramConverter->loadValueObject(array('collectionId' => 42)));
    }
}

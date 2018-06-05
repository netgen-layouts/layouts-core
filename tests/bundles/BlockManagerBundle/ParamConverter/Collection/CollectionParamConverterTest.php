<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter;
use PHPUnit\Framework\TestCase;

final class CollectionParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter
     */
    private $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new CollectionParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(['collectionId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('collection', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APICollection::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::loadValue
     */
    public function testLoadValue()
    {
        $collection = new Collection();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollection')
            ->with($this->equalTo(42))
            ->will($this->returnValue($collection));

        $this->assertEquals(
            $collection,
            $this->paramConverter->loadValue(
                [
                    'collectionId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\CollectionParamConverter::loadValue
     */
    public function testLoadValueDraft()
    {
        $collection = new Collection();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadCollectionDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($collection));

        $this->assertEquals(
            $collection,
            $this->paramConverter->loadValue(
                [
                    'collectionId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}

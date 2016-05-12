<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Service\CollectionService;

class CollectionQueryParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->getMock(CollectionService::class);

        $this->paramConverter = new CollectionQueryParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter::getSourceAttributeName
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals('query_id', $this->paramConverter->getSourceAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('query', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APIQuery::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\CollectionQueryParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadQuery')
            ->with($this->equalTo(42), $this->equalTo(Collection::STATUS_DRAFT))
            ->will($this->returnValue($query));

        self::assertEquals($query, $this->paramConverter->loadValueObject(42, Collection::STATUS_DRAFT));
    }
}

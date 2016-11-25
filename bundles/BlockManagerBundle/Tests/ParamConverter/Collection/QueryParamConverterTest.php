<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Service\CollectionService;
use PHPUnit\Framework\TestCase;

class QueryParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new QueryParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('queryId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('query', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIQuery::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadQuery')
            ->with($this->equalTo(42))
            ->will($this->returnValue($query));

        $this->assertEquals(
            $query,
            $this->paramConverter->loadValueObject(
                array(
                    'queryId' => 42,
                    'published' => true,
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::loadValueObject
     */
    public function testLoadValueObjectDraft()
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadQueryDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($query));

        $this->assertEquals(
            $query,
            $this->paramConverter->loadValueObject(
                array(
                    'queryId' => 42,
                    'published' => false,
                )
            )
        );
    }
}

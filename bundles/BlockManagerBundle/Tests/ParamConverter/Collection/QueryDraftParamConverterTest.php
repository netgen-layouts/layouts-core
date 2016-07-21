<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter;
use Netgen\BlockManager\Core\Values\Collection\QueryDraft;
use Netgen\BlockManager\API\Values\Collection\QueryDraft as APIQueryDraft;
use Netgen\BlockManager\API\Service\CollectionService;
use PHPUnit\Framework\TestCase;

class QueryDraftParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new QueryDraftParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('queryId'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('query', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIQueryDraft::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryDraftParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $item = new QueryDraft();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadQueryDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($item));

        $this->assertEquals($item, $this->paramConverter->loadValueObject(array('queryId' => 42)));
    }
}

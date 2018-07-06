<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter;
use PHPUnit\Framework\TestCase;

final class QueryParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new QueryParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        $this->assertSame(['queryId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        $this->assertSame('query', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        $this->assertSame(APIQuery::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadQuery')
            ->with($this->identicalTo(42))
            ->will($this->returnValue($query));

        $this->assertSame(
            $query,
            $this->paramConverter->loadValue(
                [
                    'queryId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\QueryParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadQueryDraft')
            ->with($this->identicalTo(42))
            ->will($this->returnValue($query));

        $this->assertSame(
            $query,
            $this->paramConverter->loadValue(
                [
                    'queryId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}

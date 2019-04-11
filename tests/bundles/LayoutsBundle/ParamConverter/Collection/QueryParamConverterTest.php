<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use PHPUnit\Framework\TestCase;

final class QueryParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new QueryParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['queryId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('query', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Query::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadQuery')
            ->with(self::identicalTo(42))
            ->willReturn($query);

        self::assertSame(
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
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $query = new Query();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadQueryDraft')
            ->with(self::identicalTo(42))
            ->willReturn($query);

        self::assertSame(
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

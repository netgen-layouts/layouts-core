<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class QueryParamConverterTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private QueryParamConverter $paramConverter;

    protected function setUp(): void
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

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadQuery')
            ->with(self::equalTo($uuid))
            ->willReturn($query);

        self::assertSame(
            $query,
            $this->paramConverter->loadValue(
                [
                    'queryId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\QueryParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $query = new Query();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadQueryDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($query);

        self::assertSame(
            $query,
            $this->paramConverter->loadValue(
                [
                    'queryId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}

<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\QueryValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(QueryValueResolver::class)]
final class QueryValueResolverTest extends TestCase
{
    private MockObject&CollectionService $collectionServiceMock;

    private QueryValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->valueResolver = new QueryValueResolver($this->collectionServiceMock);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['queryId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('query', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Query::class, $this->valueResolver->getSupportedClass());
    }

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
            $this->valueResolver->loadValue(
                [
                    'queryId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

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
            $this->valueResolver->loadValue(
                [
                    'queryId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}

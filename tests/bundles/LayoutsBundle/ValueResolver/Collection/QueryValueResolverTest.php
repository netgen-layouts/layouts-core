<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\QueryValueResolver;
use Netgen\Bundle\LayoutsBundle\ValueResolver\Status;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(QueryValueResolver::class)]
final class QueryValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private QueryValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new QueryValueResolver($this->collectionServiceStub);
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

        $uuid = Uuid::v4();

        $this->collectionServiceStub
            ->method('loadQuery')
            ->with(self::equalTo($uuid))
            ->willReturn($query);

        self::assertSame(
            $query,
            $this->valueResolver->loadValue(
                [
                    'queryId' => $uuid->toString(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $query = new Query();

        $uuid = Uuid::v4();

        $this->collectionServiceStub
            ->method('loadQueryDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($query);

        self::assertSame(
            $query,
            $this->valueResolver->loadValue(
                [
                    'queryId' => $uuid->toString(),
                    'status' => Status::Draft,
                ],
            ),
        );
    }
}

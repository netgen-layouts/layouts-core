<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Query::class)]
final class QueryTest extends TestCase
{
    public function testSetProperties(): void
    {
        $queryType = new QueryType('query_type');

        $queryUuid = Uuid::uuid4();
        $collectionUuid = Uuid::uuid4();

        $query = Query::fromArray(
            [
                'id' => $queryUuid,
                'collectionId' => $collectionUuid,
                'queryType' => $queryType,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [],
            ],
        );

        self::assertSame($queryUuid->toString(), $query->getId()->toString());
        self::assertSame($collectionUuid->toString(), $query->getCollectionId()->toString());
        self::assertSame($queryType, $query->getQueryType());
        self::assertTrue($query->isTranslatable());
        self::assertSame('en', $query->getMainLocale());
        self::assertTrue($query->isAlwaysAvailable());
        self::assertSame(['en'], $query->getAvailableLocales());
        self::assertSame('en', $query->getLocale());
    }

    public function testIsContextual(): void
    {
        $query = Query::fromArray(
            [
                'id' => Uuid::uuid4(),
                'collectionId' => Uuid::uuid4(),
                'queryType' => new QueryType('query_type'),
            ],
        );

        self::assertFalse($query->isContextual());
    }
}

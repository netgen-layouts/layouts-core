<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Query::class)]
final class QueryTest extends TestCase
{
    public function testSetProperties(): void
    {
        $queryType = new QueryType('query_type');

        $queryUuid = Uuid::v7();
        $collectionUuid = Uuid::v7();

        $query = Query::fromArray(
            [
                'id' => $queryUuid,
                'collectionId' => $collectionUuid,
                'queryType' => $queryType,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'isAlwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => new ParameterList(),
            ],
        );

        self::assertSame($queryUuid->toString(), $query->id->toString());
        self::assertSame($collectionUuid->toString(), $query->collectionId->toString());
        self::assertSame($queryType, $query->queryType);
        self::assertTrue($query->isTranslatable);
        self::assertSame('en', $query->mainLocale);
        self::assertTrue($query->isAlwaysAvailable);
        self::assertSame(['en'], $query->availableLocales);
        self::assertSame('en', $query->locale);
    }

    public function testIsContextual(): void
    {
        $query = Query::fromArray(
            [
                'id' => Uuid::v7(),
                'collectionId' => Uuid::v7(),
                'queryType' => new QueryType('query_type'),
            ],
        );

        self::assertFalse($query->isContextual);
    }
}

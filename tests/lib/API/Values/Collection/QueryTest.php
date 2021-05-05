<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class QueryTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\Layouts\API\Values\Collection\Query::getCollectionId
     * @covers \Netgen\Layouts\API\Values\Collection\Query::getId
     * @covers \Netgen\Layouts\API\Values\Collection\Query::getLocale
     * @covers \Netgen\Layouts\API\Values\Collection\Query::getMainLocale
     * @covers \Netgen\Layouts\API\Values\Collection\Query::getQueryType
     * @covers \Netgen\Layouts\API\Values\Collection\Query::isAlwaysAvailable
     * @covers \Netgen\Layouts\API\Values\Collection\Query::isTranslatable
     */
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

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\Query::isContextual
     */
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

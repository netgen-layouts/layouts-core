<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Query());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getAvailableLocales
     */
    public function testDefaultProperties(): void
    {
        $query = new Query();

        self::assertSame([], $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getLocale
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getMainLocale
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::isAlwaysAvailable
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::isTranslatable
     */
    public function testSetProperties(): void
    {
        $queryType = new QueryType('query_type');

        $query = Query::fromArray(
            [
                'id' => 42,
                'collectionId' => 30,
                'queryType' => $queryType,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [],
            ]
        );

        self::assertSame(42, $query->getId());
        self::assertSame(30, $query->getCollectionId());
        self::assertSame($queryType, $query->getQueryType());
        self::assertTrue($query->isTranslatable());
        self::assertSame('en', $query->getMainLocale());
        self::assertTrue($query->isAlwaysAvailable());
        self::assertSame(['en'], $query->getAvailableLocales());
        self::assertSame('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\Query::isContextual
     */
    public function testIsContextual(): void
    {
        $query = Query::fromArray(
            [
                'queryType' => new QueryType('query_type'),
            ]
        );

        self::assertFalse($query->isContextual());
    }
}

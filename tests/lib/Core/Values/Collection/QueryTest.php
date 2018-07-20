<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Query());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     */
    public function testDefaultProperties(): void
    {
        $query = new Query();

        $this->assertSame([], $query->getAvailableLocales());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getCollectionId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getId
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::getQueryType
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isTranslatable
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

        $this->assertSame(42, $query->getId());
        $this->assertSame(30, $query->getCollectionId());
        $this->assertSame($queryType, $query->getQueryType());
        $this->assertTrue($query->isTranslatable());
        $this->assertSame('en', $query->getMainLocale());
        $this->assertTrue($query->isAlwaysAvailable());
        $this->assertSame(['en'], $query->getAvailableLocales());
        $this->assertSame('en', $query->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Collection\Query::isContextual
     */
    public function testIsContextual(): void
    {
        $query = Query::fromArray(
            [
                'queryType' => new QueryType('query_type'),
            ]
        );

        $this->assertFalse($query->isContextual());
    }
}

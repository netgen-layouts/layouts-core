<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class ResultSetTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::count
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getCollection
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getIterator
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getLimit
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getOffset
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getResults
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::getTotalCount
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetExists
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetGet
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetSet
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::offsetUnset
     */
    public function testObject(): void
    {
        $collection = new Collection();

        $resultItem = new Result(0, new CmsItem());

        $result = ResultSet::fromArray(
            [
                'collection' => $collection,
                'results' => [$resultItem],
                'totalCount' => 15,
                'offset' => 3,
                'limit' => 5,
            ]
        );

        self::assertSame($collection, $result->getCollection());
        self::assertSame([$resultItem], $result->getResults());
        self::assertFalse($result->isContextual());
        self::assertSame(15, $result->getTotalCount());
        self::assertSame(3, $result->getOffset());
        self::assertSame(5, $result->getLimit());

        self::assertSame([$resultItem], iterator_to_array($result->getIterator()));

        self::assertCount(1, $result);

        self::assertTrue(isset($result[0]));
        self::assertSame($resultItem, $result[0]);

        try {
            $result[0] = 'new';
            self::fail('Succeeded in setting a new value to result set.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($result[0]);
            self::fail('Succeeded in unsetting a value in result set.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::isDynamic
     */
    public function testIsDynamic(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => new Query(),
                    ]
                ),
            ]
        );

        self::assertTrue($result->isDynamic());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::isDynamic
     */
    public function testIsDynamicWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => new Collection(),
            ]
        );

        self::assertFalse($result->isDynamic());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::isContextual
     */
    public function testIsContextual(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => Query::fromArray(
                            [
                                'queryType' => new QueryType('type', [], null, false),
                            ]
                        ),
                    ]
                ),
            ]
        );

        self::assertFalse($result->isContextual());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::isContextual
     */
    public function testIsContextualWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => new Collection(),
            ]
        );

        self::assertFalse($result->isContextual());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\ResultSet::isContextual
     */
    public function testIsContextualWithContextualQuery(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => Query::fromArray(
                            [
                                'queryType' => new QueryType('type', [], null, true),
                            ]
                        ),
                    ]
                ),
            ]
        );

        self::assertTrue($result->isContextual());
    }
}

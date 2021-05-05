<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class ResultSetTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::count
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::getCollection
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::getIterator
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::getLimit
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::getOffset
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::getResults
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::getTotalCount
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::offsetExists
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::offsetGet
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
            ],
        );

        self::assertSame($collection, $result->getCollection());
        self::assertSame([$resultItem], $result->getResults());
        self::assertFalse($result->isContextual());
        self::assertSame(15, $result->getTotalCount());
        self::assertSame(3, $result->getOffset());
        self::assertSame(5, $result->getLimit());

        self::assertSame([$resultItem], [...$result]);

        self::assertCount(1, $result);

        self::assertTrue(isset($result[0]));
        self::assertSame($resultItem, $result[0]);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::offsetSet
     */
    public function testSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $result = ResultSet::fromArray(
            [
                'results' => [new Result(0, new CmsItem())],
            ],
        );

        $result[0] = 'new';
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::offsetUnset
     */
    public function testUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $result = ResultSet::fromArray(
            [
                'results' => [new Result(0, new CmsItem())],
            ],
        );

        unset($result[0]);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::isDynamic
     */
    public function testIsDynamic(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => new Query(),
                    ],
                ),
            ],
        );

        self::assertTrue($result->isDynamic());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::isDynamic
     */
    public function testIsDynamicWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => new Collection(),
            ],
        );

        self::assertFalse($result->isDynamic());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::isContextual
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
                            ],
                        ),
                    ],
                ),
            ],
        );

        self::assertFalse($result->isContextual());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::isContextual
     */
    public function testIsContextualWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => new Collection(),
            ],
        );

        self::assertFalse($result->isContextual());
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\ResultSet::isContextual
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
                            ],
                        ),
                    ],
                ),
            ],
        );

        self::assertTrue($result->isContextual());
    }
}
